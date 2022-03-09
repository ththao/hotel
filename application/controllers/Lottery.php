<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lottery extends My_Controller {
    public function __construct()
    {
        parent::__construct();
        $this->layout('layout/main_bds');
    }

	public function index()
	{
	    set_time_limit(0);
	    
	    $channel_id = isset($_GET['channel_id']) && $_GET['channel_id'] ? $_GET['channel_id'] : 1;
	    $row_num = isset($_GET['row_num']) && $_GET['row_num'] ? $_GET['row_num'] : 30;
	    $digit_num = isset($_GET['digit_num']) && $_GET['digit_num'] ? $_GET['digit_num'] : 2;
	    
	    $result = $this->get_statistic($channel_id, $row_num, $digit_num);
	    
	    $this->render('lottery/index', ['result' => $result]);
	}
	
	public function add()
	{
	    $query = $this->db->select('id, channel_name, week_day')->from('lottery_channel')->get();
	    $channels = $query->result();
	    
	    if ($_POST) {
	        $import_date = $_POST['import_date'];
	        $lottery_channel_id = $_POST['lottery_channel_id'];
	        $numbers = $_POST['numbers'];
	        $numbers = preg_replace("/[^0-9\s]/", "", $numbers);
	        
	        $query = $this->db->select('id, channel_area, channel_name, week_day')->from('lottery_channel')->where('id', $lottery_channel_id)->get();
	        $channel = $query->row();
	        
	        if ($channel && ($channel->id == 1 || $channel->week_day == date('w', strtotime($import_date)) + 1)) {
	            $numbers = str_split($numbers, 1);
	            $update = '';
	            foreach ($numbers as $number) {
	                if (is_numeric($number)) {
	                    $update .= $number;
	                } else {
	                    if ($update && substr($update, -1) != ' ') {
	                        $update .= ' ';
	                    }
	                }
	            }
	            
	            $query = $this->db->select('id')->from('lottery_data')->where('lottery_channel_id', $lottery_channel_id)->where('import_date', date('Y-m-d', strtotime($import_date)))->get();
	            $l_data = $query->row();
	            
	            if ($l_data) {
	                $this->db->where('id', $l_data->id)->update('lottery_data', ['numbers' => $update]);
	            } else {
	                $this->db->insert('lottery_data', ['lottery_channel_id' => $lottery_channel_id, 'import_date' => date('Y-m-d', strtotime($import_date)), 'numbers' => $update]);
	            }
	            
	            $this->run_statistics();
	            
	            redirect('http://ksno1.com/lottery/add?channel_id=' . $_POST['lottery_channel_id'] . '&date=' . $_POST['import_date']);
	        }
	    }
	    
	    $this->render('lottery/add', ['channels' => $channels]);
	}
	
	public function statistics()
	{
	    $daily_data = [];
	    $daily_date = [];
	    $weekly_data = [];
	    $weekly_week = [];
	    
	    if ($_GET && $_GET['channel_area']) {
	        $this->db->select('channel_name, position, week_day, lottery_statistic.*');
	        $this->db->from('lottery_statistic');
	        $this->db->join('lottery_channel', 'lottery_channel.id = lottery_statistic.lottery_channel_id');
	        $this->db->where('channel_area', $_GET['channel_area']);
	        if (isset($_GET['lottery_channel_id']) && $_GET['lottery_channel_id']) {
	            if ($_GET['lottery_channel_id'] == 5 || $_GET['lottery_channel_id'] == 21) {
	                $this->db->where('lottery_channel_id IN (5,21)', null);
	            } else if ($_GET['lottery_channel_id'] == 32 || $_GET['lottery_channel_id'] == 39) {
	                $this->db->where('lottery_channel_id IN (32,39)', null);
	            } else if ($_GET['lottery_channel_id'] == 25 || $_GET['lottery_channel_id'] == 33) {
	                $this->db->where('lottery_channel_id IN (25,33)', null);
	            } else if ($_GET['lottery_channel_id'] == 27 || $_GET['lottery_channel_id'] == 29) {
	                $this->db->where('lottery_channel_id IN (27,29)', null);
	            } else {
	                $this->db->where('lottery_channel_id IN (' . $_GET['lottery_channel_id'] . ')', null);
	            }
            }
            $this->db->order_by('lottery_statistic.view_date DESC, position ASC');
            $query = $this->db->get();
            
	        $data = $query->result();
	        
	        if ($data) {
	            foreach ($data as $item) {
	                $daily_date[$item->view_date] = $item->view_date;
	                if (!isset($_GET['lottery_channel_id']) || !$_GET['lottery_channel_id']) {
    	                if (count($weekly_week) < 10) {
    	                    $weekly_week[date('W', strtotime($item->view_date))] = date('W', strtotime($item->view_date));
    	                }
	                }
	                
	                for ($i=0; $i<100; $i++) {
	                    $col = ($i == 0 ? '00' : ($i < 10 ? ('0' . $i) : $i));
	                    $col_field = 'number_' . ($i == 0 ? '00' : ($i < 10 ? ('0' . $i) : $i));
	                    $col_notes_field = $col_field . '_notes';
	                    
	                    if (!isset($daily_data[$col])) {
	                        $daily_data[$col] = [];
	                    }
	                    $daily_data[$col][$item->position][$item->view_date] = $item->$col_field ? $item->$col_field : 0;
	                    
	                    if (!isset($weekly_data[$col])) {
	                        $weekly_data[$col] = [];
	                    }
	                    if (!isset($weekly_data[$col][$item->position])) {
	                        $weekly_data[$col][$item->position] = [];
	                    }
	                    if (!isset($_GET['lottery_channel_id']) || !$_GET['lottery_channel_id'] || !is_numeric($_GET['lottery_channel_id'])) {
        	                if (count($weekly_week) < 10) {
        	                    if (!isset($weekly_data[$col][$item->position][date('W', strtotime($item->view_date))])) {
        	                        $weekly_data[$col][$item->position][date('W', strtotime($item->view_date))] = ['count' => 0, 'notes' => ''];
        	                    }
        	                    $weekly_data[$col][$item->position][date('W', strtotime($item->view_date))]['count'] += ($item->$col_field ? $item->$col_field : 0);
        	                    if ($weekly_data[$col][$item->position][date('W', strtotime($item->view_date))]['notes']) {
        	                        $weekly_data[$col][$item->position][date('W', strtotime($item->view_date))]['notes'] .= ($item->$col_notes_field ? (',' . $item->$col_notes_field) : '');
    	                        } else {
    	                            $weekly_data[$col][$item->position][date('W', strtotime($item->view_date))]['notes'] .= ($item->$col_notes_field ? $item->$col_notes_field : '');
    	                        }
    	                    }
	                    }
	                }
	                $daily_data['total'] = [1 => []];
	                for ($i=0; $i<10; $i++) {
	                    $col = $i . 'x';
	                    $col_field = 'number_' . $col;
	                    
	                    if (!isset($daily_data[$col])) {
	                        $daily_data[$col][1] = [];
	                    }
	                    if (!isset($daily_data[$col][1][$item->view_date])) {
	                        $daily_data[$col][1][$item->view_date] = 0;
	                    }
	                    $daily_data[$col][1][$item->view_date] += $item->$col_field ? $item->$col_field : 0;
	                    
	                    if (!isset($_GET['lottery_channel_id']) || !$_GET['lottery_channel_id'] || !is_numeric($_GET['lottery_channel_id'])) {
    	                    if (!isset($weekly_data[$col])) {
    	                        $weekly_data[$col][1] = [];
    	                    }
        	                if (count($weekly_week) < 10) {
        	                    if (!isset($weekly_data[$col][1][date('W', strtotime($item->view_date))])) {
        	                        $weekly_data[$col][1][date('W', strtotime($item->view_date))] = ['count' => 0, 'notes' => ''];
        	                    }
        	                    $weekly_data[$col][1][date('W', strtotime($item->view_date))]['count'] += ($item->$col_field ? $item->$col_field : 0);
    	                    }
	                    }
	                    
	                    $col = 'x' . $i;
	                    $col_field = 'number_' . $col;
	                    
	                    if (!isset($daily_data[$col])) {
	                        $daily_data[$col] = [];
	                    }
	                    if (!isset($daily_data[$col][1][$item->view_date])) {
	                        $daily_data[$col][1][$item->view_date] = 0;
	                    }
	                    $daily_data[$col][1][$item->view_date] += $item->$col_field ? $item->$col_field : 0;
	                    
	                    if (!isset($_GET['lottery_channel_id']) || !$_GET['lottery_channel_id'] || !is_numeric($_GET['lottery_channel_id'])) {
    	                    if (!isset($weekly_data[$col])) {
    	                        $weekly_data[$col][1] = [];
    	                    }
        	                if (count($weekly_week) < 10) {
        	                    if (!isset($weekly_data[$col][1][date('W', strtotime($item->view_date))])) {
        	                        $weekly_data[$col][1][date('W', strtotime($item->view_date))] = ['count' => 0, 'notes' => ''];
        	                    }
        	                    $weekly_data[$col][1][date('W', strtotime($item->view_date))]['count'] += ($item->$col_field ? $item->$col_field : 0);
    	                    }
	                    }
	                }
	                
                    if (!isset($daily_data['xx'])) {
                        $daily_data['xx'][1] = [];
                    }
                    if (!isset($daily_data['xx'][1][$item->view_date])) {
                        $daily_data['xx'][1][$item->view_date] = 0;
                    }
                    $daily_data['xx'][1][$item->view_date] += $item->number_xx ? $item->number_xx : 0;
                    
                    if (!isset($_GET['lottery_channel_id']) || !$_GET['lottery_channel_id'] || !is_numeric($_GET['lottery_channel_id'])) {
	                    if (!isset($weekly_data['xx'])) {
	                        $weekly_data['xx'][1] = [];
	                    }
    	                if (count($weekly_week) < 10) {
    	                    if (!isset($weekly_data['xx'][1][date('W', strtotime($item->view_date))])) {
    	                        $weekly_data['xx'][1][date('W', strtotime($item->view_date))] = ['count' => 0, 'notes' => ''];
    	                    }
    	                    $weekly_data['xx'][1][date('W', strtotime($item->view_date))]['count'] += ($item->number_xx ? $item->number_xx : 0);
	                    }
                    }
	            }
	        }
	    }
	    
	    $query = $this->db->select('id, channel_area, channel_name, week_day')->from('lottery_channel')->get();
	    $channels = $query->result();
	    
	    $this->render('lottery/statistics', ['channels' => $channels, 'daily_data' => $daily_data, 'weekly_data' => $weekly_data, 'daily_date' => $daily_date, 'weekly_week' => $weekly_week]);
	}
	
	public function run_statistics() {
	    set_time_limit(0);
	    
	    $query = $this->db->select('lottery_channel_id, import_date, numbers')->from('lottery_data')
	       ->where('NOT EXISTS (SELECT id FROM lottery_statistic WHERE lottery_statistic.lottery_channel_id = lottery_data.lottery_channel_id AND view_date = import_date)', null)
	       ->get();
	    
       $data = $query->result();
       
       $records = [];
       if ($data) {
           foreach ($data as $index => $row) {
               $records[$index] = [
                   'lottery_channel_id' => $row->lottery_channel_id,
                   'view_date' => $row->import_date
               ];
               $numbers = explode(' ', $row->numbers);
               
               if ($numbers) {
                   foreach ($numbers as $number) {
                       if (!is_numeric($number)) {
                           continue;
                       }
                       $digit_2 = $number % 100;
                       $digit_3 = $number % 1000;
                       
                       if (isset($records[$index]['number_' . ($digit_2 < 10 ? ('0' . $digit_2) : $digit_2)])) {
                           $records[$index]['number_' . ($digit_2 < 10 ? ('0' . $digit_2) : $digit_2)] += 1;
                       } else {
                           $records[$index]['number_' . ($digit_2 < 10 ? ('0' . $digit_2) : $digit_2)] = 1;
                       }
                       if (isset($records[$index]['number_' . ($digit_2 < 10 ? ('0' . $digit_2) : $digit_2) . '_notes'])) {
                           $records[$index]['number_' . ($digit_2 < 10 ? ('0' . $digit_2) : $digit_2) . '_notes'] .= ',' . ($digit_3 < 10 ? ('00' . $digit_3) : ($digit_3 < 100 ? ('0' . $digit_3) : $digit_3));
                       } else {
                           $records[$index]['number_' . ($digit_2 < 10 ? ('0' . $digit_2) : $digit_2) . '_notes'] = ($digit_3 < 10 ? ('00' . $digit_3) : ($digit_3 < 100 ? ('0' . $digit_3) : $digit_3));
                       }
                       
                       for ($i=0; $i<10; $i++) {
                           if ($i*10 < $digit_2 && $digit_2 < ($i+1)*10) {
                               if (isset($records[$index]['number_' . $i . 'x'])) {
                                   $records[$index]['number_' . $i . 'x'] += 1;
                               } else {
                                   $records[$index]['number_' . $i . 'x'] = 1;
                               }
                           }
                           if ($digit_2 % 10 == $i) {
                               if (isset($records[$index]['number_x'. $i])) {
                                   $records[$index]['number_x'. $i] += 1;
                               } else {
                                   $records[$index]['number_x'. $i] = 1;
                               }
                           }
                       }
                       
                       if ($digit_2%10 == intdiv($digit_2,10)) {
                           if (isset($records[$index]['number_xx'])) {
                               $records[$index]['number_xx'] += 1;
                           } else {
                               $records[$index]['number_xx'] = 1;
                           }
                       }
                   }
               }
           }
       }
       
       if ($records) {
           foreach ($records as $record) {
               $query = $this->db->select('id')->from('lottery_statistic')->where('lottery_channel_id', $record['lottery_channel_id'])->where('view_date', $record['view_date'])->get();
               $db_record = $query->row();
               
               if ($db_record) {
                   $this->db->where('id', $db_record->id)->update('lottery_statistic', $record);
               } else {
                   $this->db->insert('lottery_statistic', $record);
               }
           }
       }
	}
	
	private function get_statistic($channel_ids, $row_num, $digit_num = 2) {
	    $result = [];
	    
	    $query = $this->db->select('import_date, numbers')->from('lottery_data')->where('lottery_channel_id IN (' . $channel_ids . ')', null)->order_by('import_date DESC')->limit($row_num)->get();
	    $rows = $query->result();
	    
	    $data = [];
	    if ($rows) {
	        foreach ($rows as $index => $row) {
	            $numbers = str_replace(' ', '', $row->numbers);
	            $data[$index]['numbers'] = explode(' ', $row->numbers);
	            $data[$index]['digits'] = str_split($numbers, 1);
	            $data[$index]['import_date'] = $row->import_date;
	        }
	    }
	    
	    if ($data) {
	        foreach ($data as $index => $date_data) {
	            if (!isset($data[$index + 1])) {
	                break;
	            }
	            $previous_date_date = $data[$index + 1];
	            
	            $all_combinations = [];
	            
	            foreach ($previous_date_date['digits'] as $p1 => $d1) {
	                foreach ($previous_date_date['digits'] as $p2 => $d2) {
	                    if ($digit_num > 2) {
	                        foreach ($previous_date_date['digits'] as $p3 => $d3) {
	                            if (($p1 != $p2 || $p2 != $p3 || $p3 != $p1) && !isset($all_combinations[$p1 . '-' . $p2 . '-' . $p3]) && !isset($all_combinations[$p2 . '-' . $p1 . '-' . $p3])
	                                && !isset($all_combinations[$p1 . '-' . $p3 . '-' . $p2]) && !isset($all_combinations[$p2 . '-' . $p3 . '-' . $p1]) && !isset($all_combinations[$p3 . '-' . $p1 . '-' . $p2]) && !isset($all_combinations[$p3 . '-' . $p2 . '-' . $p1])) {
	                                    $all_combinations[$p1 . '-' . $p2 . '-' . $p3] = $d1 . $d2 . $d3;
	                                    
	                                    $found = false;
	                                    if (isset($date_data['numbers']) && $date_data['numbers']) {
	                                        foreach ($date_data['numbers'] as $number) {
	                                            if ($number % (pow(10, $digit_num)) == intval($d1 . $d2 . $d3)) {
	                                                $found = true;
	                                                break;
	                                            }
	                                        }
	                                    }
	                                    
	                                    $result[$p1 . '-' . $p2 . '-' . $p3]['pos'] = $p1 . '-' . $p2 . '-' . $p3;
	                                    if ($found) {
	                                        $result[$p1 . '-' . $p2 . '-' . $p3]['dates'][$previous_date_date['import_date']] = 1;
	                                    } else {
	                                        $result[$p1 . '-' . $p2 . '-' . $p3]['dates'][$previous_date_date['import_date']] = 0;
	                                    }
	                                }
	                        }
	                    } else {
	                        if ($p1 != $p2 && !isset($all_combinations[$p1 . '-' . $p2]) && !isset($all_combinations[$p2 . '-' . $p1])) {
	                            $all_combinations[$p1 . '-' . $p2] = $d1 . $d2;
	                            
	                            $found = false;
	                            if (isset($date_data['numbers']) && $date_data['numbers']) {
	                                foreach ($date_data['numbers'] as $number) {
	                                    if ($number % (pow(10, $digit_num)) == intval($d1 . $d2)) {
	                                        $found = true;
	                                        break;
	                                    }
	                                }
	                            }
	                            
	                            $result[$p1 . '-' . $p2]['pos'] = $p1 . '-' . $p2;
	                            if ($found) {
	                                $result[$p1 . '-' . $p2]['dates'][$previous_date_date['import_date']] = 1;
	                            } else {
	                                $result[$p1 . '-' . $p2]['dates'][$previous_date_date['import_date']] = 0;
	                            }
	                        }
	                    }
	                }
	            }
	        }
	    }
	    
	    foreach ($result as $pos => $pos_data) {
	        $counts = array_count_values($pos_data['dates']);
	        $pos_data['count'] = isset($counts[1]) ? $counts[1] : 0;
	        $result[$pos] = $pos_data;
	    }
	    
	    usort($result, function($a, $b) {
	        return $a['count'] <= $b['count'];
	    });
	    
	    $ret = [];
        $count = $result[0]['count'];
        $potential_numbers = [];
        
        foreach ($result as $result_item) {
            if ($count != $result_item['count']) {
                $enough = true;
                foreach ($potential_numbers as $date_potential_numbers) {
                    if (count($date_potential_numbers) < 5) {
                        $enough = false;
                        break;
                    }
                }
                
                if ($enough) {
                    break;
                }
            }
            
            $pos = explode('-', $result_item['pos']);
            
            foreach ($data as $data_value) {
                $result_item['numbers'][$data_value['import_date']] = $data_value['digits'][$pos[0]] . $data_value['digits'][$pos[1]];
                if (!isset($potential_numbers[$data_value['import_date']])) {
                    $potential_numbers[$data_value['import_date']] = [];
                }
                $potential_numbers[$data_value['import_date']][] = $data_value['digits'][$pos[0]] . $data_value['digits'][$pos[1]];
            }
            $ret[] = $result_item;
        }
        
        if ($potential_numbers) {
            $channel_ids = explode(',', $channel_ids);
            if (count($channel_ids) == 1) {
                foreach ($potential_numbers as $date => $date_potential_numbers) {
                    sort($date_potential_numbers);
                    $this->db->where('lottery_channel_id', $channel_ids[0])->where('import_date', $date)->where('potential_numbers IS NULL', null)->update('lottery_data', ['potential_numbers' => implode(',', $date_potential_numbers)]);
                }
            }
            
            echo '<pre>';
            print_r($potential_numbers);die;
        }
        
        return $ret;
	}
}