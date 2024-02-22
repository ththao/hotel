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
            'used_items_price' => $this->rent_item_model->getPrice($rent->id),
            'hourly' => $rent->hourly
        );
        
        switch ($this->session->userdata('user_id')) {
            case 1:
                $data = $this->calculatePrice($room, $rent, $data);
                break;
            
            default:
                $data = $this->calculatePrice($room, $rent, $data);
                break;
        }
        
        $this->update($rent->id, $data);
        
        return true;
    }
    
    public function calculatePrice($room, $rent, $data) {
        $note = '';
        
        if ($rent->negotiate_price) {
            $data['total_price'] = $rent->negotiate_price + $data['used_items_price'];
            $note = 'Giá thỏa thuận ' . $rent->negotiate_price;
            
            if ($data['used_items_price']) {
                $note .= ($note ? ';' : '') . 'Nước uống=' . number_format($data['used_items_price'], 0);
            }
            if ($room->extra_price && $rent->human > 0) {
                $extra_price = $rent->human * $room->extra_price;
                $note .= ($note ? ';' : '') . 'Phụ thu=' . number_format($extra_price, 0);
                $data['total_price'] += $extra_price;
            }
            
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
                $data['total_price'] = $price['amount'] + $data['used_items_price'];
                $note = $price['note'];
                
            } else {
                $data['total_price'] = $room->daily_price * floor($hours / 24) + $data['used_items_price'];
                $note = floor($hours / 24) . ' ngày=' . number_format($room->daily_price * floor($hours / 24), 0);
                
                $price = $this->calculateByDays($room, $data, $hours - (floor($hours / 24) * 24));
                $data['total_price'] += $price['amount'];
                $note .= ($note ? ';' : '') . $price['note'];
            }
            
            if ($data['used_items_price']) {
                $note .= ($note ? ';' : '') . 'Nước uống=' . number_format($data['used_items_price'], 0);
            }
            if ($room->extra_price && $rent->human > 0) {
                $extra_price = $rent->human * $room->extra_price;
                $note .= ($note ? ';' : '') . 'Phụ thu=' . number_format($extra_price, 0);
                $data['total_price'] += $extra_price;
            }
            
            if (intval($room->discount) > 0) {
                $note .= ($note ? ';' : '') . 'Khuyến mãi=-' . number_format($room->discount * ceil($hours/24), 0);
                $data['total_price'] -= $room->discount * ceil($hours/24);
            }
        }
        $data['note'] = $note;
        return $data;
    }
    
    private function calculateByDays($room, $data, $hours) {
        if ($hours <= 6) {
            if ($data['check_out'] > strtotime(date('Y-m-d 00:01')) && $data['check_out'] < strtotime(date('Y-m-d 05:00'))) {
                return array(
                    'amount' => $room->night_price,
                    'note' => 'Qua đêm=' . number_format($room->night_price, 0)
                );
            } else if ($data['check_in'] > strtotime(date('Y-m-d 00:01')) && $data['check_in'] < strtotime(date('Y-m-d 04:00'))) {
                return array(
                    'amount' => $room->night_price,
                    'note' => 'Qua đêm=' . number_format($room->night_price, 0)
                );
            } else {
                return $this->calculateByHours($room, $hours);
            }
            
        } else if ($hours <= 14 && time() <= strtotime(date('Y-m-d 12:30'))) {
            return array(
                'amount' => $room->night_price,
                'note' => 'Nửa ngày=' . number_format($room->night_price, 0)
            );
            
        }  else if ($hours <= 14 && time() > strtotime(date('Y-m-d 12:30')) && (strtotime(date('Y-m-d 12:00')) - $data['check_in'])/3600 < 7) {
            return array(
                'amount' => $room->night_price,
                'note' => 'Nửa ngày=' . number_format($room->night_price, 0)
            );
            
        } else if ($hours <= 18) {
            $price = $room->night_price;
            $note = 'Nửa ngày=' . number_format($room->night_price, 0);
            
            if ($data['check_out'] > strtotime(date('Y-m-d 12:30'))) {
                $mod_hours = ($data['check_out'] - strtotime(date('Y-m-d 12:00')))/3600;
                
                if ($mod_hours - floor($mod_hours) > 0.5) {
                    $price += ceil($mod_hours) * $room->next_hourly_price;
                } else if ($mod_hours - floor($mod_hours) > 0.15) {
                    $price += (floor($mod_hours) * $room->next_hourly_price + $room->next_hourly_price / 2);
                } else {
                    $price += floor($mod_hours) * $room->next_hourly_price;
                }
                
                if ($price > $room->daily_price) {
                    return array(
                        'amount' => $room->daily_price,
                        'note' => number_format($hours, 1) . ' giờ=' . number_format($room->daily_price, 0)
                    );
                } else {
                    $note .= ($note ? ';' : '') . number_format($mod_hours, 1) . ' giờ tiếp theo (tính từ 12h)=' . number_format($price - $room->night_price, 0);
                }
            }
            return array(
                'amount' => $price,
                'note' => $note
            );
            
        } else if ($hours <= 24) {
            return array(
                'amount' => $room->daily_price,
                'note' => number_format($hours, 1) . ' giờ=' . number_format($room->daily_price, 0)
            );
        }
    }
    
    private function calculateByHours($room, $hours) {
        $price = $room->hourly_price;
        $note = 'Giờ đầu=' . number_format($room->hourly_price, 0);
        if ($hours > 1) {
            if (floor($hours - 1) > 0) {
                $price += $room->next_hourly_price * floor($hours - 1);
            }
            if ($hours - floor($hours) > 0.5) {
                $price += $room->next_hourly_price;
            } else if ($hours - floor($hours) > 0.15) {
                $price += $room->next_hourly_price/2;
            }
            $note .= ($note ? ';' : '') . number_format($hours - 1, 1) . ' giờ tiếp theo=' . number_format($price - $room->hourly_price, 0);
        }
        
        if ($price > $room->night_price) {
            $note = number_format($price, 1) . ' giờ=' . number_format($room->night_price, 0);
            return array('amount' => $room->night_price, 'note' => $note);
        } else {
            return array('amount' => $price, 'note' => $note);
        }
    }
}