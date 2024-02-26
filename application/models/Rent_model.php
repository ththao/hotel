<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rent_model extends MY_Model {
    
    public function __construct()
    {
        $this->set_table_name('rent');
        
        // Call the CI_Model constructor
        parent::__construct();
    }
    
    public function getList($year, $month, $date)
    {
        $year = $year ? $year : date('Y');
        $month = $month ? $month : date('m');
        if ($date) {
            $start = strtotime($year . '-' . $month . '-' . $date . ' 00:00:00');
            $end = strtotime($year . '-' . $month . '-' . $date . ' 23:59:59');
        } else {
            $start = strtotime($year . '-' . $month . '-01 00:00:00');
            $t = date('t', strtotime($year . '-' . $month));
            $end = strtotime($year . '-' . $month . '-' . $t . ' 23:59:59');
        }
        
        $query = $this->db->query('
	        SELECT rent.*, room.name AS room_name
	        FROM rent INNER JOIN room ON room.id = rent.room_id AND room.user_id = rent.user_id
	        WHERE rent.user_id = ' . $this->session->userdata('user_id') . '
    			AND ((rent.check_out >= ' . $start . ' AND rent.check_out <= ' . $end . ') OR rent.check_out IS NULL)
    		ORDER BY rent.check_out IS NULL, rent.check_out'
            );
        return $query->result();
    }
    
    public function getReport($year, $month)
    {
        $year = $year ? $year : date('Y');
        if ($month) {
            $start = strtotime($year . '-' . $month . '-01 00:00:00');
            $t = date('t', strtotime($year . '-' . $month));
            $end = strtotime($year . '-' . $month . '-' . $t . ' 23:59:59');
        } else {
            $start = strtotime($year . '-01-01 00:00:00');
            $t = date('t', strtotime($year . '-12'));
            $end = strtotime($year . '-12-' . $t . ' 23:59:59');
        }
        
        $sql = 'SELECT check_out, used_items_price, total_price FROM rent
	        WHERE user_id = ' . $this->session->userdata('user_id') . '
            AND check_out >= ' . $start . ' AND check_out <= ' . $end . '
            ORDER BY check_out';
        $query = $this->db->query($sql);
        
        $records = $query->result();
        
        $data = array();
        $total = array('items_total' => 0, 'rent_total' => 0);
        foreach ($records as $record) {
            if (isset($data[date('Y-m-d', $record->check_out)])) {
                $data[date('Y-m-d', $record->check_out)]['items_total'] += $record->used_items_price;
                $data[date('Y-m-d', $record->check_out)]['rent_total'] += $record->total_price;
            } else {
                $data[date('Y-m-d', $record->check_out)] = array(
                    'items_total' => $record->used_items_price,
                    'rent_total' => $record->total_price
                );
            }
            $total['items_total'] += $record->used_items_price;
            $total['rent_total'] += $record->total_price;
        }
        
        $data['DOANH THU'] = $total;
        
        $sql = 'SELECT SUM(amount) AS amount FROM paid
	        WHERE user_id = ' . $this->session->userdata('user_id') . '
            AND paid_date LIKE "%' . $month . '-' . $year. '"
            LIMIT 1';
        $query1 = $this->db->query($sql);
        
        $records = $query1->result();
        
        if ($records) {
            $paid = $records[0];
            $data['CHI PHÍ'] = array(
                'items_total' => 0,
                'rent_total' => $paid->amount * -1
            );
        }
        
        return $data;
    }
    
    public function getItemsUsed($id)
    {
        $sql = '
            SELECT item.id, item.name, item.icon_class, item.big_icon_class, rent_item.quantity, rent_item.unit_price
            FROM rent_item
            INNER JOIN item ON item.id = rent_item.item_id
            WHERE item.user_id = ' . $this->session->userdata('user_id') . ' AND rent_item.rent_id = ' . $id;
        
        $query = $this->db->query($sql);
        return $query->result();
    }
    
    public function getItemsReceived($id)
    {
        $this->db->select('
            rent_receive.id, rent_receive.rent_id, rent_receive.early_return,
            rent_receive.type, card.name, COALESCE(card.number, rent_receive.number) AS number,
            card.nation, card.birthday, card.address, card.gender
        ');
        $this->db->from('rent_receive');
        $this->db->join('card', 'card.id = rent_receive.card_id', 'LEFT OUTER');
        $this->db->where(array('rent_receive.rent_id' => $id));
        $this->db->order_by('rent_receive.type');
        $query = $this->db->get();
        
        return $query->result();
    }
    
    public function findWorkingRoom($room_id)
    {
        $this->db->from('rent');
        $this->db->where('user_id', $this->session->userdata('user_id'));
        $this->db->where('room_id', $room_id);
        $this->db->where('(check_out IS NULL OR check_out = 0)');
        
        $res = $this->db->get();
        if ($data = $res->result()) {
            return $data[0];
        }
        return null;
    }
    
    public function checkin($room_id, $hourly=1)
    {
        $checkin_time = time();
        if (date('H') >= 23) {
            $hourly = 0;
        }
        
        $data = array(
            'user_id' => $this->session->userdata('user_id'),
            'room_id' => $room_id,
            'check_in' => $checkin_time,
            'human' => 0,
            'hourly' => $hourly
        );
        return $this->save($data);
    }
    
    public function checkout($rent)
    {
        $this->load->model('room_model');
        $room = $this->room_model->findOne(array('id' => $rent->room_id));
        if (!$room) {
            return false;
        }
        
        $data = array(
            'check_in' => $rent->check_in,
            'check_out' => time(),
            'additional_fee' => $this->rent_additional_fee_model->getFee($rent->id),
            'fee_list' => $this->rent_additional_fee_model->getFeeList($rent->id),
            'used_items_price' => $this->rent_item_model->getPrice($rent->id),
            'hourly' => $rent->hourly
        );
        $data = $this->calculatePrice($room, $rent, $data);
        
        unset($data['fee_list']);
        $this->update($rent->id, $data);
        
        return true;
    }
    
    
    /**
     * Hoàng Vy + 158A
     * @param Object $room
     * @param integer $check_in
     * @param integer $check_out
     * @return array
     */
    private function calculateDayPrice($room, $check_in, $check_out) {
        $hours = ($check_out - $check_in) / 3600;
        
        $threshold = ceil(($room->night_price - $room->hourly_price) / $room->next_hourly_price) + 1;
        
        if ($hours < $threshold) {
            if (date('H', $check_out) >= 1 && date('H', $check_out) <= 6) {
                return ['amount' => $room->night_price, 'note' => date('H:i d/m', $check_in) . ' - ' . date('H:i d/m', $check_out) . '=' . number_format($room->night_price, 0)];
            } else {
                return $this->calculateHourlyPrice($room, $hours);
            }
            
        } else {
            if (date('H', $check_in) >= 21 || $hours <= 12) {
                return ['amount' => $room->night_price, 'note' => date('H:i d/m', $check_in) . ' - ' . date('H:i d/m', $check_out) . '=' . number_format($room->night_price, 0)];
            } else {
                return ['amount' => $room->daily_price, 'note' => date('H:i d/m', $check_in) . ' - ' . date('H:i d/m', $check_out) . '=' . number_format($room->daily_price, 0)];
            }
        }
    }
    
    /**
     * Hoàng Vy + 158A
     * @param Object $room
     * @param float $hours
     * @return string[]|NULL[]|string[]|number[]
     */
    private function calculateHourlyPrice($room, $hours) {
        $price = $room->hourly_price;
        $note = 'Giờ đầu=' . number_format($room->hourly_price, 0);
        if ($hours > 1) {
            if (floor($hours - 1) > 0) {
                $price += $room->next_hourly_price * floor($hours - 1);
            }
            if ($hours - floor($hours) > 0.15) {
                $price += $room->next_hourly_price;
            }
            $note .= ($note ? ';' : '') . number_format($hours - 1, 1) . ' giờ tiếp theo=' . number_format($price - $room->hourly_price, 0);
        }
        
        if ($price > $room->night_price) {
            $note = number_format($hours, 1) . ' giờ=' . number_format($room->night_price, 0);
            return ['amount' => $room->night_price, 'note' => $note];
        } else {
            return ['amount' => $price, 'note' => $note];
        }
    }
    
    /**
     * Huệ Thiên + Phú Quốc
     * @param Object $room
     * @param array $data
     * @param float $hours
     * @return array
     */
    private function calculateByDays($room, $data, $hours) {
        $query = $this->db->select('*')->from('user_settings')->where('user_id', $room->user_id)->get();
        $settings = $query->row();
        
        if (!$settings) {
            $query = $this->db->select('*')->from('user_settings')->where('user_id', 1)->get();
            $settings = $query->row();
        }
        
        if ($hours <= $settings->hourly_hours) {
            if ($data['check_out'] > strtotime(date('Y-m-d 00:01')) && $data['check_out'] < strtotime(date('Y-m-d 05:00'))) {
                return ['amount' => $room->night_price, 'note' => 'Qua đêm=' . number_format($room->night_price, 0)];
            } else if ($data['check_in'] > strtotime(date('Y-m-d 00:01')) && $data['check_in'] < strtotime(date('Y-m-d 04:00'))) {
                return ['amount' => $room->night_price, 'note' => 'Qua đêm=' . number_format($room->night_price, 0)];
            } else {
                return $this->calculateByHours($room, $hours, $settings);
            }
            
        } else if ($hours <= $settings->half_day_hours && time() <= strtotime(date('Y-m-d 12:30'))) {
            return ['amount' => $room->night_price, 'note' => 'Nửa ngày=' . number_format($room->night_price, 0)];
            
        }  else if ($hours <= $settings->half_day_hours && time() > strtotime(date('Y-m-d 12:30')) && (strtotime(date('Y-m-d 12:00')) - $data['check_in'])/3600 < 7) {
            return ['amount' => $room->night_price, 'note' => 'Nửa ngày=' . number_format($room->night_price, 0)];
            
        } else if ($hours <= $settings->full_day_hours) {
            $price = $room->night_price;
            $note = 'Nửa ngày=' . number_format($room->night_price, 0);
            
            if ($data['check_out'] > strtotime(date('Y-m-d 12:30'))) {
                $mod_hours = ($data['check_out'] - strtotime(date('Y-m-d 12:00')))/3600;
                
                if ($mod_hours - floor($mod_hours) > ($settings->full_hour_minutes / 60)) {
                    $price += ceil($mod_hours) * $room->next_hourly_price;
                } else if ($mod_hours - floor($mod_hours) > ($settings->half_hour_minutes / 60)) {
                    $price += (floor($mod_hours) * $room->next_hourly_price + $room->next_hourly_price / 2);
                } else {
                    $price += floor($mod_hours) * $room->next_hourly_price;
                }
                
                if ($price > $room->daily_price) {
                    return ['amount' => $room->daily_price, 'note' => number_format($hours, 1) . ' giờ=' . number_format($room->daily_price, 0)];
                } else {
                    $note .= ($note ? ';' : '') . number_format($mod_hours, 1) . ' giờ tiếp theo (tính từ 12h)=' . number_format($price - $room->night_price, 0);
                }
            }
            return ['amount' => $price, 'note' => $note];
            
        } else if ($hours <= 24) {
            return ['amount' => $room->daily_price, 'note' => number_format($hours, 1) . ' giờ=' . number_format($room->daily_price, 0)];
        }
    }
    
    /**
     * Huệ Thiên + Phú Quốc
     * @param Object $room
     * @param float $hours
     * @param Object $settings
     * @return string[]|NULL[]|string[]|number[]
     */
    private function calculateByHours($room, $hours, $settings) {
        $price = $room->hourly_price;
        $note = 'Giờ đầu=' . number_format($room->hourly_price, 0);
        if ($hours > 1) {
            if (floor($hours - 1) > 0) {
                $price += $room->next_hourly_price * floor($hours - 1);
            }
            if ($hours - floor($hours) > ($settings->full_hour_minutes / 60)) {
                $price += $room->next_hourly_price;
            } else if ($hours - floor($hours) > ($settings->half_hour_minutes / 60)) {
                $price += $room->next_hourly_price/2;
            }
            $note .= ($note ? ';' : '') . number_format($hours - 1, 1) . ' giờ tiếp theo=' . number_format($price - $room->hourly_price, 0);
        }
        
        if ($price > $room->night_price) {
            $note = number_format($hours, 1) . ' giờ=' . number_format($room->night_price, 0);
            return ['amount' => $room->night_price, 'note' => $note];
        } else {
            return ['amount' => $price, 'note' => $note];
        }
    }
    
    /**
     * Main function to calculate price
     * @param Object $room
     * @param Object $rent
     * @param array $data
     * @return array
     */
    public function calculatePrice($room, $rent, $data) {
        $note = '';
        
        // Khi có thỏa thuận thì không cần tính ngày giờ
        if ($rent->negotiate_price) {
            $data['total_price'] = $rent->negotiate_price + $data['used_items_price'] + $data['additional_fee'] - $rent->discount;
            $note = 'Giá thỏa thuận ' . $rent->negotiate_price;
            
        } else {
            
            // Hoàng Vy, 158A
            if (in_array($room->user_id, [2,3,6,7])) {
                // Khách thuê trong ngày, hoặc dưới 24h
                if (date('Y-m-d', $data['check_in']) == date('Y-m-d', $data['check_out']) || (($data['check_out'] - $data['check_in']) / 3600) <= 24) {
                    $hours = ($data['check_out'] - $data['check_in']) / 3600;
                    $price = $this->calculateByDays($room, $data, $hours);
                    $data['total_price'] = $price['amount'] + $data['used_items_price'] + $data['additional_fee'] - $rent->discount;
                    $note .= ($note ? ';' : '') . $price['note'];
                    
                    // Khách thuê nhiều ngày
                } else {
                    $check_in = $data['check_in'];
                    $this_day_checkout = strtotime(date('Y-m-d', $check_in) . ' 12:00');
                    
                    // Trường hợp khách vào trước 12h trưa
                    if ($data['check_out'] > $this_day_checkout && $check_in < $this_day_checkout) {
                        if (($this_day_checkout - $check_in) <= 3600 && ($this_day_checkout - $check_in) > 600) {
                            $data['total_price'] = (isset($data['total_price']) ? $data['total_price'] : 0) + $room->next_hourly_price;
                            $note .= ($note ? ';' : '') . 'Vào trước 12h ngày ' . date('d/m') . ' 1 tiếng=' . number_format($room->next_hourly_price, 0);
                        } else if (($this_day_checkout - $check_in) <= 2*3600 && ($this_day_checkout - $check_in) > (3600+600)) {
                            $data['total_price'] = (isset($data['total_price']) ? $data['total_price'] : 0) + 2*$room->next_hourly_price;
                            $note .= ($note ? ';' : '') . 'Vào trước 12h ngày ' . date('d/m') . ' 2 tiếng=' . number_format(2*$room->next_hourly_price, 0);
                        } else if (($this_day_checkout - $check_in) <= 3*3600 && ($this_day_checkout - $check_in) > (3600*2 + 600)) {
                            $data['total_price'] = (isset($data['total_price']) ? $data['total_price'] : 0) + 3*$room->next_hourly_price;
                            $note .= ($note ? ';' : '') . 'Vào trước 12h ngày ' . date('d/m') . ' 3 tiếng=' . number_format(3*$room->next_hourly_price, 0);
                        } else {
                            $price = $this->calculateDayPrice($room, $check_in, $this_day_checkout);
                            $data['total_price'] = (isset($data['total_price']) ? $data['total_price'] : 0) + $price['amount'];
                            $note .= ($note ? ';' : '') . $price['note'];
                        }
                        $check_in = $this_day_checkout;
                    }
                    
                    // Tính tiền cho mỗi ngày vào lúc 12h trưa
                    while (true) {
                        $next_day_checkout = strtotime(date('Y-m-d', strtotime(date('Y-m-d', $check_in) . '+1 day')) . ' 12:00');
                        if ($data['check_out'] > $next_day_checkout) {
                            $price = $this->calculateDayPrice($room, $check_in, $next_day_checkout);
                            $data['total_price'] = (isset($data['total_price']) ? $data['total_price'] : 0) + $price['amount'];
                            $note .= ($note ? ';' : '') . $price['note'];
                            
                            // Cách tính tiền nếu khách trả phòng sau 12h trưa
                            if (in_array($room->user_id, [3,7]) && ($data['check_out'] - $next_day_checkout) > 600 && ($data['check_out'] - $next_day_checkout) <= 3600) {
                                if ($room->user_id == 7) {
                                    $data['total_price'] += 30000;
                                    $note .= ($note ? ';' : '') . 'Quá giờ=30,000';
                                } else if ($room->user_id == 3) {
                                    $data['total_price'] += 20000;
                                    $note .= ($note ? ';' : '') . 'Quá giờ=20,000';
                                }
                                break;
                            }
                            
                            $check_in = $next_day_checkout;
                        } else {
                            $price = $this->calculateDayPrice($room, $check_in, $data['check_out']);
                            $data['total_price'] = (isset($data['total_price']) ? $data['total_price'] : 0) + $price['amount'];
                            $note .= ($note ? ';' : '') . $price['note'];
                            break;
                        }
                    }
                    
                    $data['total_price'] = $data['total_price'] + $data['used_items_price'] + $data['additional_fee'] - $rent->discount;
                }
                
                // Huệ Thiên, Phú Quốc
            } else {
                $hours = ($data['check_out'] - $data['check_in']) / 3600;
                $threshold = ceil(($room->night_price - $room->hourly_price) / $room->next_hourly_price) + 1;
                
                if ($hours > $threshold || (date('H', $data['check_out']) >= 1 && date('H', $data['check_out']) <= 5)) {
                    $data['hourly'] = 0;
                } else {
                    $data['hourly'] = 1;
                }
                
                if ($hours <= 24) {
                    $price = $this->calculateByDays($room, $data, $hours);
                    $data['total_price'] = $price['amount'] + $data['used_items_price'] + $data['additional_fee'] - $rent->discount;
                    $note = $price['note'];
                    
                } else {
                    $data['total_price'] = $room->daily_price * floor($hours / 24) + $data['used_items_price'] + $data['additional_fee'] - $rent->discount;
                    $note = floor($hours / 24) . ' ngày=' . number_format($room->daily_price * floor($hours / 24), 0);
                    
                    $price = $this->calculateByDays($room, $data, $hours - (floor($hours / 24) * 24));
                    $data['total_price'] += $price['amount'];
                    $note .= ($note ? ';' : '') . $price['note'];
                }
            }
        }
        
        // Giảm giá ở phần ghi chú
        if (intval($rent->discount) > 0) {
            $note .= ($note ? ';' : '') . 'Giảm giá=-' . number_format($rent->discount, 0);
        }
        // Tính số tiền mua nước uống / mì gói / bánh kẹo
        if ($data['used_items_price']) {
            $note .= ($note ? ';' : '') . 'Nước uống=' . number_format($data['used_items_price'], 0);
        }
        // Phụ thu dịch vụ
        if ($data['additional_fee'] && $data['fee_list']) {
            foreach ($data['fee_list'] as $fee) {
                $note .= ($note ? ';' : '') . ($fee->notes ? $fee->notes : 'Phụ Thu') . '=' . number_format($fee->amount, 0);
            }
        }
        
        $data['discount'] = $rent->discount;
        $data['note'] = $note;
        $data['negotiate_price'] = $rent->negotiate_price;
        $data['notes'] = $rent->notes;
        return $data;
    }
}