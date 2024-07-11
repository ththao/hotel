<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lottery extends My_Controller {
    public function __construct()
    {
        parent::__construct();
        
	    if (!$this->checkLoggedIn()) {
	    	if ($this->input->is_ajax_request()) {
	    		echo json_encode(array('status' => 0));
	    	} else {
            	redirect('/auth/login');
	    	}
        }
        
        $this->layout('layout/main_bds');
    }
    
    public function reverse_pair()
    {
        $this->db->select('lottery_data.import_date, lottery_data.numbers')->from('lottery_data')
            ->join('lottery_channel', 'lottery_channel.id = lottery_data.lottery_channel_id')
            ->where('lottery_channel.position IN (1,2)', null)
            ->order_by('lottery_data.import_date DESC')->limit(730);
            
        if (isset($_GET['c'])) {
            $this->db->where('lottery_channel.channel_area', $_GET['c']);
        } else {
            $this->db->where('lottery_channel.channel_area', 'MN');
        }
        $query = $this->db->get();
        $result = $query->result();
        
        $data = [];
        if ($result) {
            $day_data = [];
            foreach ($result as $item) {
                $numbers = explode(' ', $item->numbers);
                foreach ($numbers as $number) {
                    $day_data[$item->import_date][$number % 100] = $number % 100;
                }
            }
            
            foreach ($day_data as $date => $day_numbers) {
                $data[$date . ' (' . date('N', strtotime($date)) . ')'] = [];
                foreach ($day_numbers as $num1) {
                    foreach ($day_numbers as $num2) {
                        if ($num1%10 == floor($num2/10) && $num2%10 == floor($num1/10) && $num1 < $num2) {
                            if (isset($_GET['n'])) {
                                if (floor($num1/10) == $_GET['n'] || floor($num2/10) == $_GET['n']) {
                                    $data[$date . ' (' . date('N', strtotime($date)) . ')'][] = $num1 . '-' . $num2;
                                }
                            } else {
                                $data[$date . ' (' . date('N', strtotime($date)) . ')'][] = $num1 . '-' . $num2;
                            }
                        }
                    }
                }
            }
        }
        
        echo '<pre>';
        print_r($data);
    }
    
    public function pair()
    {
        $this->db->select('lottery_data.import_date, lottery_data.numbers')->from('lottery_data')
            ->join('lottery_channel', 'lottery_channel.id = lottery_data.lottery_channel_id')
            ->where('lottery_channel.channel_area', 'MN')
            ->order_by('lottery_data.import_date DESC')->limit(730);
        $query = $this->db->get();
        $result = $query->result();
        
        $data = [];
        if ($result) {
            $day_data = [];
            foreach ($result as $item) {
                $numbers = explode(' ', $item->numbers);
                foreach ($numbers as $number) {
                    $day_data[$item->import_date][$number % 100] = $number % 100;
                }
            }
            
            foreach ($day_data as $date => $day_numbers) {
                foreach ($day_numbers as $num1) {
                    foreach ($day_numbers as $num2) {
                        if ($num1 != $num2 && $num1 < $num2) {
                            if (!isset($data[$num1 . '-' . $num2])) {
                                $data[$num1 . '-' . $num2] = [];
                            }
                            
                            $data[$num1 . '-' . $num2][] = $date;
                        }
                    }
                }
            }
        }
        
        echo '<pre>';
        print_r($data);
        
        $count_data = [];
        foreach ($data as $pair => $days) {
            $count_data[$pair] = count($days);
        }
        
        
        echo '<pre>';
        print_r($count_data);
    }
    
    public function days()
    {
        set_time_limit(0);
        ini_set('memory_limit', -1);
        
        $this->db->select('lottery_data.import_date, lottery_data.numbers')->from('lottery_data')->join('lottery_channel', 'lottery_channel.id = lottery_data.lottery_channel_id', 'INNER');
        if (isset($_GET['c'])) {
            $this->db->where('lottery_channel.channel_area', $_GET['c']);
        } else {
            $this->db->where('lottery_channel.channel_area', 'MN');
        }
        if (isset($_GET['start']) && $_GET['start'] && isset($_GET['end']) && $_GET['end']) {
            $this->db->where('lottery_data.import_date >= "' . $_GET['start'] . '" AND lottery_data.import_date <= "' . $_GET['end'] . '"');
        }
        $this->db->where('position IN (1,2)', NULL)->order_by('lottery_data.import_date DESC');
        $query = $this->db->get();
        
        $result = $query->result();
        
        $data = [];
        
        if ($result) {
            foreach ($result as $item) {
                $numbers = explode(' ', $item->numbers);
                
                foreach ($numbers as $number) {
                    if (trim($number) != '') {
                        $number = $number%100;
                        $data[$item->import_date][$number] = isset($data[$item->import_date][$number]) ? ($data[$item->import_date][$number] + 1) : 1;
                    }
                }
            }
        }
        
        $result = [];
        foreach ($data as $date => $numbers) {
            ksort($numbers);
            $d = [];
            foreach ($numbers as $number1 => $val1) {
                foreach ($numbers as $number2 => $val2) {
                    foreach ($numbers as $number3 => $val3) {
                        foreach ($numbers as $number4 => $val4) {
                            if ($number1 < $number2 && $number2 < $number3 && $number3 < $number4) {
                                $d[$number1.','.$number2.','.$number3.','.$number4] = $val1 + $val2 + $val3 + $val4;
                            }
                        }
                    }
                }
            }
            
            $result[$date] = $d;
        }
        
        $ss = [];
        foreach ($result as $date => $d) {
            foreach ($d as $ns => $count) {
                $ss[$ns] = isset($ss[$ns]) ? ($ss[$ns] . ',' . $date) : $date;
            }
        }
        echo '<pre>';
        print_r($ss);
    }
    
    public function  results()
    {
        $this->db->select('*')->from('lottery_forecast')->where('view_date < "' . date('Y-m-d') . '"', null)->where('view_date >= "2023-01-01"', null);
        $this->db->order_by('view_date');
        $query = $this->db->get();
        $result = $query->result();
        
        $base_cost = 30;
        $base_earn = 80;
        $data = [];
        foreach ($result as $date_data) {
            $mn_w_r = array_count_values(explode(',', $date_data->mn_w_r));
            $mn_m_r = array_count_values(explode(',', $date_data->mn_m_r));
            $mn_y_r = array_count_values(explode(',', $date_data->mn_y_r));
            
            $mn_w_2 = explode(',', $date_data->mn_w_2);
            $mn_m_2 = explode(',', $date_data->mn_m_2);
            $mn_y_2 = explode(',', $date_data->mn_y_2);
            
            $w = array_merge(explode(',', $date_data->mn_w_1), explode(',', $date_data->mn_w_2));
            $m = array_merge(explode(',', $date_data->mn_m_1), explode(',', $date_data->mn_m_2));
            $y = array_merge(explode(',', $date_data->mn_y_1), explode(',', $date_data->mn_y_2));
            $same_values = array_intersect($w, $m, $y);
            
            $all_earn = 0;
            if ($same_values) {
                foreach ($same_values as $same_value) {
                    $all_earn += $this->array_count_values_of($same_value, $mn_w_r) * $base_earn;
                }
            }
            
            $data[$date_data->view_date]['mn'] = [
                'w' => $mn_w_2[2],
                'm' => $mn_m_2[2],
                'y' => $mn_y_2[2],
                'all' => $same_values,
                'w_cost' => -1 * $base_cost,
                'w_earn' => $this->array_count_values_of($mn_w_2[2], $mn_w_r) * $base_earn,
                'm_cost' => -1 * $base_cost,
                'm_earn' => $this->array_count_values_of($mn_m_2[2], $mn_m_r) * $base_earn,
                'y_cost' => -1 * $base_cost,
                'y_earn' => $this->array_count_values_of($mn_y_2[2], $mn_y_r) * $base_earn,
                'w_m_cost' => -2 * $base_cost,
                'w_m_earn' => ($this->array_count_values_of($mn_w_2[2], $mn_w_r) + $this->array_count_values_of($mn_m_2[2], $mn_m_r)) * $base_earn,
                'm_y_cost' => -2 * $base_cost,
                'm_y_earn' => ($this->array_count_values_of($mn_y_2[2], $mn_y_r) + $this->array_count_values_of($mn_m_2[2], $mn_m_r)) * $base_earn,
                'w_y_cost' => -2 * $base_cost,
                'w_y_earn' => ($this->array_count_values_of($mn_w_2[2], $mn_w_r) + $this->array_count_values_of($mn_y_2[2], $mn_y_r)) * $base_earn,
                'w_m_y_cost' => -3 * $base_cost,
                'w_m_y_earn' => ($this->array_count_values_of($mn_w_2[2], $mn_w_r) + $this->array_count_values_of($mn_m_2[2], $mn_m_r) + $this->array_count_values_of($mn_y_2[2], $mn_y_r)) * $base_earn,
                'all_cost' => count($same_values) * $base_cost,
                'all_earn' => $all_earn
            ];
            
            
            $mt_w_r = array_count_values(explode(',', $date_data->mt_w_r));
            $mt_m_r = array_count_values(explode(',', $date_data->mt_m_r));
            $mt_y_r = array_count_values(explode(',', $date_data->mt_y_r));
            
            $mt_w_2 = explode(',', $date_data->mt_w_2);
            $mt_m_2 = explode(',', $date_data->mt_m_2);
            $mt_y_2 = explode(',', $date_data->mt_y_2);
            
            $w = array_merge(explode(',', $date_data->mt_w_1), explode(',', $date_data->mt_w_2));
            $m = array_merge(explode(',', $date_data->mt_m_1), explode(',', $date_data->mt_m_2));
            $y = array_merge(explode(',', $date_data->mt_y_1), explode(',', $date_data->mt_y_2));
            $same_values = array_intersect($w, $m, $y);
            
            $all_earn = 0;
            if ($same_values) {
                foreach ($same_values as $same_value) {
                    $all_earn += $this->array_count_values_of($same_value, $mt_w_r) * $base_earn;
                }
            }
            
            $data[$date_data->view_date]['mt'] = [
                'w' => $mt_w_2[2],
                'm' => $mt_m_2[2],
                'y' => $mt_y_2[2],
                'all' => $same_values,
                'w_cost' => -1 * $base_cost,
                'w_earn' => $this->array_count_values_of($mt_w_2[2], $mt_w_r) * $base_earn,
                'm_cost' => -1 * $base_cost,
                'm_earn' => $this->array_count_values_of($mt_m_2[2], $mt_m_r) * $base_earn,
                'y_cost' => -1 * $base_cost,
                'y_earn' => $this->array_count_values_of($mt_y_2[2], $mt_y_r) * $base_earn,
                'w_m_cost' => -2 * $base_cost,
                'w_m_earn' => ($this->array_count_values_of($mt_w_2[2], $mt_w_r) + $this->array_count_values_of($mt_m_2[2], $mt_m_r)) * $base_earn,
                'm_y_cost' => -2 * $base_cost,
                'm_y_earn' => ($this->array_count_values_of($mt_y_2[2], $mt_y_r) + $this->array_count_values_of($mt_m_2[2], $mt_m_r)) * $base_earn,
                'w_y_cost' => -2 * $base_cost,
                'w_y_earn' => ($this->array_count_values_of($mt_w_2[2], $mt_w_r) + $this->array_count_values_of($mt_y_2[2], $mt_y_r)) * $base_earn,
                'w_m_y_cost' => -3 * $base_cost,
                'w_m_y_earn' => ($this->array_count_values_of($mt_w_2[2], $mt_w_r) + $this->array_count_values_of($mt_m_2[2], $mt_m_r) + $this->array_count_values_of($mt_y_2[2], $mt_y_r)) * $base_earn,
                'all_cost' => count($same_values) * $base_cost,
                'all_earn' => $all_earn
            ];
        }
        
        //print_r($data);die;
        $this->render('lottery/results', ['data' => $data]);
    }
    
    private function array_count_values_of($value, $array) {
        return isset($array[$value]) ? $array[$value] : 0;
    }
    
    public function mbdb()
    {
        $this->db->select('import_date, numbers')->from('lottery_data')->where('lottery_channel_id', 1);
	    $this->db->order_by('import_date');
        $query = $this->db->get();
        $result = $query->result();
        
        $db = [];
        $d2 = [];
        if ($result) {
            foreach ($result as $item) {
                $numbers = explode(' ', $item->numbers);
                $db[$numbers[0]%10][] = $item->import_date;
                $d2[$numbers[23]%10][] = $item->import_date;
                $d2[$numbers[24]%10][] = $item->import_date;
                $d2[$numbers[25]%10][] = $item->import_date;
                $d2[$numbers[26]%10][] = $item->import_date;
            }
        }
        
        $data = [];
        if ($db) {
            foreach ($db as $number => $dates) {
                $delay = 0;
                $delay_date = '';
                foreach ($dates as $d_index => $date) {
                    if ($d_index > 0) {
                        $length = (strtotime($date) - strtotime($dates[$d_index - 1]))/86400;
                        if ($delay < $length) {
                            $delay = $length;
                            $delay_date = $dates[$d_index - 1];
                        }
                    }
                }
                $data[$number] = ['delay' => $delay, 'delay_date' => $delay_date, 'last_date' => $dates[count($dates) - 1], 'delaying' => floor((time() - strtotime($dates[count($dates) - 1]))/86400)];
            }
        }
        $data2 = [];
        if ($d2) {
            foreach ($d2 as $number => $dates) {
                $delay = 0;
                $delay_date = '';
                foreach ($dates as $d_index => $date) {
                    if ($d_index > 0) {
                        $length = (strtotime($date) - strtotime($dates[$d_index - 1]))/86400;
                        if ($delay < $length) {
                            $delay = $length;
                            $delay_date = $dates[$d_index - 1];
                        }
                    }
                }
                $data2[$number] = ['delay' => $delay, 'delay_date' => $delay_date, 'last_date' => $dates[count($dates) - 1], 'delaying' => floor((time() - strtotime($dates[count($dates) - 1]))/86400)];
            }
        }
        
        echo '<pre>';
        print_r($data);
        echo '<pre>';
        print_r($data2);
    }
    
    public function mb()
    {
        $numer_index = isset($_GET['vt']) && $_GET['vt'] ? $_GET['vt'] : 0;
        $max_days = isset($_GET['md']) && $_GET['md'] ? $_GET['md'] : 10;
        $this->db->select('import_date, numbers')->from('lottery_data')->where('lottery_channel_id', 1);
	    $this->db->order_by('import_date DESC');
	    $this->db->limit(700);
        $query = $this->db->get();
        $result = $query->result();
        
        $data = [];
        if ($result) {
            foreach ($result as $item) {
                $numbers = explode(' ', $item->numbers);
                
                foreach ($numbers as $index => $number) {
                    $numbers[$index] = $number%100;
                }
                
                $data[$item->import_date] = ['db' => $numbers[$numer_index], 'return' => [], 'numbers' => $numbers, 'longest' => 0];
                
                foreach ($data as $import_date => $date_data) {
                    if (in_array($numbers[$numer_index], $date_data['numbers'])) {
                        if (strtotime($item->import_date) < strtotime($import_date) && ((strtotime($import_date) - strtotime($item->import_date))/86400 <= $max_days)) {
                            $data[$item->import_date]['return'][] = $import_date;
                        }
                    }
                }
            }
        }
        
        $this->render('lottery/mb', ['data' => $data]);
    }
    
    public function mn()
    {
        $channel_id = isset($_GET['channel_id']) && $_GET['channel_id'] ? $_GET['channel_id'] : 5;
        $numer_index = isset($_GET['vt']) && $_GET['vt'] ? $_GET['vt'] : 0;
        $max_weeks = isset($_GET['mw']) && $_GET['mw'] ? $_GET['mw'] : 10;
        $this->db->select('import_date, numbers')->from('lottery_data')->where('lottery_channel_id', $channel_id);
	    $this->db->order_by('import_date DESC');
	    $this->db->limit(700);
        $query = $this->db->get();
        $result = $query->result();
        
        $db = [];
        $data = [];
        if ($result) {
            foreach ($result as $item) {
                $numbers = explode(' ', $item->numbers);
                
                foreach ($numbers as $index => $number) {
                    $numbers[$index] = $number%100;
                }
                
                $data[$item->import_date] = ['db' => $numbers[$numer_index], 'return' => [], 'numbers' => $numbers, 'longest' => 0];
                
                foreach ($data as $import_date => $date_data) {
                    if (in_array($numbers[$numer_index], $date_data['numbers'])) {
                        if (strtotime($item->import_date) < strtotime($import_date) && ((strtotime($import_date) - strtotime($item->import_date))/(86400*7) <= $max_weeks)) {
                            $data[$item->import_date]['return'][] = $import_date;
                        }
                    }
                }
            }
        }
        
        $this->render('lottery/mb', ['data' => $data, 'db' => []]);
    }
    
    public function all_forecast()
    {
        set_time_limit(0);
        
        if (isset($_GET['date']) && $_GET['date']) {
            if (isset($_GET['date']) && $_GET['date']) {
    	        $week_day = date('w', strtotime($_GET['date'])) + 1;
    	    } else {
    	        $week_day = date('w') + 1;
    	    }
    	    
    	    if (isset($_GET['print']) && $_GET['print']) {
                $this->run_forecast(isset($_GET['area']) && $_GET['area'] ? $_GET['area'] : 'MN', $week_day, isset($_GET['limit']) && $_GET['limit'] ? $_GET['limit'] : 60);
                die;
    	    } else {
    	    
                $this->run_forecast('MN', $week_day, 14);
                $this->run_forecast('MN', $week_day, 60);
                $this->run_forecast('MN', $week_day, 730);
                
                $this->run_forecast('MT', $week_day, 14);
                $this->run_forecast('MT', $week_day, 60);
                $this->run_forecast('MT', $week_day, 730);
                
                if ($_GET['date'] <= date('Y-m-d')) {
                    $this->run_forecast('MB', $week_day, 7);
                    $this->run_forecast('MB', $week_day, 30);
                    $this->run_forecast('MB', $week_day, 365);
                }
    	    }
        } else {
            for ($i=0; $i<=6; $i++) {
                $_GET['date'] = date('Y-m-d', strtotime('+' . $i . 'days'));
                
                if (isset($_GET['date']) && $_GET['date']) {
        	        $week_day = date('w', strtotime($_GET['date'])) + 1;
        	    } else {
        	        $week_day = date('w') + 1;
        	    }
        	    
                $this->run_forecast('MN', $week_day, 14);
                $this->run_forecast('MN', $week_day, 60);
                $this->run_forecast('MN', $week_day, 730);
                
                $this->run_forecast('MT', $week_day, 14);
                $this->run_forecast('MT', $week_day, 60);
                $this->run_forecast('MT', $week_day, 730);
                
                if ($i == 0) {
                    $this->run_forecast('MB', $week_day, 7);
                    $this->run_forecast('MB', $week_day, 30);
                    $this->run_forecast('MB', $week_day, 365);
                }
            }
        }
    }
    
    public function delay()
    {
        $this->db->select('*')->from('lottery_forecast');
        $this->db->where('view_date <= "' . date('Y-m-d' . '"'), null);
	    $this->db->order_by('view_date DESC');
	    $this->db->limit(isset($_GET['limit']) ? $_GET['limit'] : 30);
        $query = $this->db->get();
        $result = $query->result_array();
        
        $data = [];
        if ($result) {
            foreach ($result as $item) {
                if (isset($_GET['v']) && $_GET['v'] == 'sm1') {
                    $forcast_numbers = $item['mn_m_1'];
                } else if (isset($_GET['v']) && $_GET['v'] == 'sw1') {
                    $forcast_numbers = $item['mn_w_1'];
                } else if (isset($_GET['v']) && $_GET['v'] == 'sy1') {
                    $forcast_numbers = $item['mn_y_1'];
                } else if (isset($_GET['v']) && $_GET['v'] == 'sm2') {
                    $forcast_numbers = $item['mn_m_2'];
                } else if (isset($_GET['v']) && $_GET['v'] == 'sy2') {
                    $forcast_numbers = $item['mn_y_2'];
                } else if (isset($_GET['v']) && $_GET['v'] == 'sw2') {
                    $forcast_numbers = $item['mn_w_2'];
                } else if (isset($_GET['v']) && $_GET['v'] == 'mm1') {
                    $forcast_numbers = $item['mt_m_1'];
                } else if (isset($_GET['v']) && $_GET['v'] == 'mw1') {
                    $forcast_numbers = $item['mt_w_1'];
                } else if (isset($_GET['v']) && $_GET['v'] == 'my1') {
                    $forcast_numbers = $item['mt_y_1'];
                } else if (isset($_GET['v']) && $_GET['v'] == 'mm2') {
                    $forcast_numbers = $item['mt_m_2'];
                } else if (isset($_GET['v']) && $_GET['v'] == 'my2') {
                    $forcast_numbers = $item['mt_y_2'];
                } else if (isset($_GET['v']) && $_GET['v'] == 'mw2') {
                    $forcast_numbers = $item['mt_w_2'];
                }
                
                if ($forcast_numbers) {
                    $week_numbers = explode(',', $forcast_numbers);
                    $week_number = $week_numbers[isset($_GET['pos']) ? $_GET['pos'] : 2];
                    
                    $data[$item['view_date']] = ['number' => $week_number, 'correct_date' => 0, 'next_date' => 0, 'prev_date' => 0];
                    
                    $this->db->select('lottery_data.numbers, lottery_data.import_date')->from('lottery_data');
            	    $this->db->join('lottery_channel', 'lottery_channel.id = lottery_data.lottery_channel_id', 'INNER');
            	    if (in_array($_GET['v'], ['sw1', 'sm1', 'sy1', 'sw2', 'sm2', 'sy2'])) {
            	        $this->db->where('lottery_channel.channel_area', 'MN');
            	    } else {
            	        $this->db->where('lottery_channel.channel_area', 'MT');
            	    }
            	    $this->db->where('lottery_data.import_date = "' . $item['view_date'] . '"', null);
            	    $this->db->where('lottery_channel.position IN (1,2)', null);
                    $query = $this->db->get();
            	    $rows = $query->result();
            	    
            	    if ($rows) {
            	        foreach ($rows as $row) {
            	            $numbers = explode(' ', $row->numbers);
            	            if ($numbers) {
            	                foreach ($numbers as $number) {
            	                    if ($week_number == $number%100) {
            	                        $data[$item['view_date']]['correct_date'] = 1;
            	                        break;
            	                    }
            	                }
            	            }
            	        }
            	    }
            	    
            	    $this->db->select('lottery_data.numbers, lottery_data.import_date')->from('lottery_data');
            	    $this->db->join('lottery_channel', 'lottery_channel.id = lottery_data.lottery_channel_id', 'INNER');
            	    if (in_array($_GET['v'], ['sw1', 'sm1', 'sy1', 'sw2', 'sm2', 'sy2'])) {
            	        $this->db->where('lottery_channel.channel_area', 'MN');
            	    } else {
            	        $this->db->where('lottery_channel.channel_area', 'MT');
            	    }
            	    $this->db->where('lottery_data.import_date = "' . date('Y-m-d', strtotime($item['view_date'] . '+1 days')) . '"', null);
            	    $this->db->where('lottery_channel.position IN (1,2)', null);
                    $query = $this->db->get();
            	    $rows = $query->result();
            	    
            	    if ($rows) {
            	        foreach ($rows as $row) {
            	            $numbers = explode(' ', $row->numbers);
            	            if ($numbers) {
            	                foreach ($numbers as $number) {
            	                    if ($week_number == $number%100) {
            	                        $data[$item['view_date']]['next_date'] = 1;
            	                        break;
            	                    }
            	                }
            	            }
            	        }
            	    }
            	    
            	    $this->db->select('lottery_data.numbers, lottery_data.import_date')->from('lottery_data');
            	    $this->db->join('lottery_channel', 'lottery_channel.id = lottery_data.lottery_channel_id', 'INNER');
            	    if (in_array($_GET['v'], ['sw1', 'sm1', 'sy1', 'sw2', 'sm2', 'sy2'])) {
            	        $this->db->where('lottery_channel.channel_area', 'MN');
            	    } else {
            	        $this->db->where('lottery_channel.channel_area', 'MT');
            	    }
            	    $this->db->where('lottery_data.import_date = "' . date('Y-m-d', strtotime($item['view_date'] . '-1 days')) . '"', null);
            	    $this->db->where('lottery_channel.position IN (1,2)', null);
                    $query = $this->db->get();
            	    $rows = $query->result();
            	    
            	    if ($rows) {
            	        foreach ($rows as $row) {
            	            $numbers = explode(' ', $row->numbers);
            	            if ($numbers) {
            	                foreach ($numbers as $number) {
            	                    if ($week_number == $number%100) {
            	                        $data[$item['view_date']]['prev_date'] = 1;
            	                        break;
            	                    }
            	                }
            	            }
            	        }
            	    }
                }
            }
        }
        
        $this->render('lottery/delay', ['data' => $data]);
    }
    
    public function all()
    {
        $this->db->select('*')->from('lottery_forecast');
	    $this->db->order_by('view_date DESC');
	    $this->db->limit(30);
        $query = $this->db->get();
        $result = $query->result_array();
        
        $data = [];
        if ($result) {
            foreach ($result as $item) {
                if (!$item['mn_o_1'] || !$item['mn_o_2']) {
                    $week_day = date('w', strtotime($item['view_date'])) + 1;
                    $this->db->select('lottery_data.lottery_channel_id, lottery_data.import_date, lottery_data.potential_numbers, lottery_channel.position')->from('lottery_data');
            	    $this->db->join('lottery_channel', 'lottery_channel.id = lottery_data.lottery_channel_id', 'INNER');
            	    $this->db->where('lottery_channel.channel_area', 'MN');
            	    $this->db->where('lottery_channel.week_day', $week_day);
            	    $this->db->where('lottery_data.import_date < "' . $item['view_date'] . '"', null);
            	    $this->db->where('lottery_channel.position IN (1,2)', null);
            	    $this->db->order_by('lottery_data.import_date DESC');
            	    $this->db->limit(2);
                    $query = $this->db->get();
            	    $rows = $query->result();
            	    
            	    if ($rows) {
            	        foreach ($rows as $row) {
            	            $this->db->where('id', $item['id'])->update('lottery_forecast', ['mn_o_' . $row->position => $row->potential_numbers]);
            	            $item['mn_o_' . $row->position] = $row->potential_numbers;
            	        }
            	    }
                }
                if (!$item['mt_o_1'] || !$item['mt_o_2']) {
                    $week_day = date('w', strtotime($item['view_date'])) + 1;
                    $this->db->select('lottery_data.lottery_channel_id, lottery_data.import_date, lottery_data.potential_numbers, lottery_channel.position')->from('lottery_data');
            	    $this->db->join('lottery_channel', 'lottery_channel.id = lottery_data.lottery_channel_id', 'INNER');
            	    $this->db->where('lottery_channel.channel_area', 'MT');
            	    $this->db->where('lottery_channel.week_day', $week_day);
            	    $this->db->where('lottery_data.import_date < "' . $item['view_date'] . '"', null);
            	    $this->db->where('lottery_channel.position IN (1,2)', null);
            	    $this->db->order_by('lottery_data.import_date DESC');
            	    $this->db->limit(2);
                    $query = $this->db->get();
            	    $rows = $query->result();
            	    
            	    if ($rows) {
            	        foreach ($rows as $row) {
            	            $this->db->where('id', $item['id'])->update('lottery_forecast', ['mt_o_' . $row->position => $row->potential_numbers]);
            	            $item['mn_t_' . $row->position] = $row->potential_numbers;
            	        }
            	    }
                }
                if (!$item['mb_o_1'] && $item['view_date'] <= date('Y-m-d')) {
                    $this->db->select('lottery_data.lottery_channel_id, lottery_data.import_date, lottery_data.potential_numbers, lottery_channel.position')->from('lottery_data');
            	    $this->db->join('lottery_channel', 'lottery_channel.id = lottery_data.lottery_channel_id', 'INNER');
            	    $this->db->where('lottery_channel.channel_area', 'MB');
            	    $this->db->where('lottery_data.import_date < "' . $item['view_date'] . '"', null);
            	    $this->db->where('lottery_channel.position IN (1)', null);
            	    $this->db->order_by('lottery_data.import_date DESC');
            	    $this->db->limit(1);
                    $query = $this->db->get();
            	    $rows = $query->result();
            	    
            	    if ($rows) {
            	        foreach ($rows as $row) {
            	            $this->db->where('id', $item['id'])->update('lottery_forecast', ['mb_o_' . $row->position => $row->potential_numbers]);
            	            $item['mn_b_' . $row->position] = $row->potential_numbers;
            	        }
            	    }
                }
                
                if (!$item['mn_o_r']) {
                    $this->db->select('lottery_data.import_date, lottery_data.numbers')->from('lottery_data');
                    $this->db->join('lottery_channel', 'lottery_channel.id = lottery_data.lottery_channel_id', 'INNER');
            	    $this->db->where('lottery_data.import_date', $item['view_date']);
            	    $this->db->where('lottery_channel.channel_area', 'MN');
            	    $this->db->where('lottery_channel.position IN (1,2)', null);
                    $query = $this->db->get();
            	    $rows = $query->result();
            	    
            	    if ($rows) {
            	        $mn_o_r = [];
            	        $mn_w_r = [];
            	        $mn_m_r = [];
            	        $mn_y_r = [];
            	        foreach ($rows as $index => $row) {
            	            $numbers = explode(' ', $row->numbers);
            	            if ($numbers) {
            	                foreach ($numbers as $number) {
            	                    $n = $number%100;
            	                    $n = $n < 10 ? ('0' . $n) : $n;
            	                    if (in_array($n, explode(',', $item['mn_o_1'])) || in_array($n, explode(',', $item['mn_o_2']))) {
            	                        $mn_o_r[] = $n;
            	                    }
            	                    if (in_array($n, explode(',', $item['mn_w_1'])) || in_array($n, explode(',', $item['mn_w_2']))) {
            	                        $mn_w_r[] = $n;
            	                    }
            	                    if (in_array($n, explode(',', $item['mn_m_1'])) || in_array($n, explode(',', $item['mn_m_2']))) {
            	                        $mn_m_r[] = $n;
            	                    }
            	                    if (in_array($n, explode(',', $item['mn_y_1'])) || in_array($n, explode(',', $item['mn_y_2']))) {
            	                        $mn_y_r[] = $n;
            	                    }
            	                }
            	            }
            	        }
            	        
            	        $this->db->where('id', $item['id'])->update('lottery_forecast', ['mn_o_r' => implode(',', $mn_o_r), 'mn_w_r' => implode(',', $mn_w_r), 'mn_m_r' => implode(',', $mn_m_r), 'mn_y_r' => implode(',', $mn_y_r)]);
            	        $item['mn_o_r'] = implode(',', $mn_o_r);
            	        $item['mn_w_r'] = implode(',', $mn_w_r);
            	        $item['mn_m_r'] = implode(',', $mn_m_r);
            	        $item['mn_y_r'] = implode(',', $mn_y_r);
            	    }
                }
                if (!$item['mt_o_r']) {
                    $this->db->select('lottery_data.import_date, lottery_data.numbers')->from('lottery_data');
                    $this->db->join('lottery_channel', 'lottery_channel.id = lottery_data.lottery_channel_id', 'INNER');
            	    $this->db->where('lottery_data.import_date', $item['view_date']);
            	    $this->db->where('lottery_channel.channel_area', 'MT');
            	    $this->db->where('lottery_channel.position IN (1,2)', null);
                    $query = $this->db->get();
            	    $rows = $query->result();
            	    
            	    if ($rows) {
            	        $mt_o_r = [];
            	        $mt_w_r = [];
            	        $mt_m_r = [];
            	        $mt_y_r = [];
            	        foreach ($rows as $index => $row) {
            	            $numbers = explode(' ', $row->numbers);
            	            if ($numbers) {
            	                foreach ($numbers as $number) {
            	                    $n = $number%100;
            	                    $n = $n < 10 ? ('0' . $n) : $n;
            	                    if (in_array($n, explode(',', $item['mt_o_1'])) || in_array($n, explode(',', $item['mt_o_2']))) {
            	                        $mt_o_r[] = $n;
            	                    }
            	                    if (in_array($n, explode(',', $item['mt_w_1'])) || in_array($n, explode(',', $item['mt_w_2']))) {
            	                        $mt_w_r[] = $n;
            	                    }
            	                    if (in_array($n, explode(',', $item['mt_m_1'])) || in_array($n, explode(',', $item['mt_m_2']))) {
            	                        $mt_m_r[] = $n;
            	                    }
            	                    if (in_array($n, explode(',', $item['mt_y_1'])) || in_array($n, explode(',', $item['mt_y_2']))) {
            	                        $mt_y_r[] = $n;
            	                    }
            	                }
            	            }
            	        }
            	        
            	        $this->db->where('id', $item['id'])->update('lottery_forecast', ['mt_o_r' => implode(',', $mt_o_r), 'mt_w_r' => implode(',', $mt_w_r), 'mt_m_r' => implode(',', $mt_m_r), 'mt_y_r' => implode(',', $mt_y_r)]);
            	        $item['mt_o_r'] = implode(',', $mt_o_r);
            	        $item['mt_w_r'] = implode(',', $mt_w_r);
            	        $item['mt_m_r'] = implode(',', $mt_m_r);
            	        $item['mt_y_r'] = implode(',', $mt_y_r);
            	    }
                }
                if (!$item['mb_o_r']) {
                    $this->db->select('lottery_data.import_date, lottery_data.numbers')->from('lottery_data');
                    $this->db->join('lottery_channel', 'lottery_channel.id = lottery_data.lottery_channel_id', 'INNER');
            	    $this->db->where('lottery_data.import_date', $item['view_date']);
            	    $this->db->where('lottery_channel.channel_area', 'MB');
                    $query = $this->db->get();
            	    $rows = $query->result();
            	    
            	    if ($rows) {
            	        $mb_o_r = [];
            	        $mb_w_r = [];
            	        $mb_m_r = [];
            	        $mb_y_r = [];
            	        foreach ($rows as $index => $row) {
            	            $numbers = explode(' ', $row->numbers);
            	            if ($numbers) {
            	                foreach ($numbers as $number) {
            	                    $n = $number%100;
            	                    $n = $n < 10 ? ('0' . $n) : $n;
            	                    if (in_array($n, explode(',', $item['mb_o_1']))) {
            	                        $mb_o_r[] = $n;
            	                    }
            	                    if (in_array($n, explode(',', $item['mb_w_1']))) {
            	                        $mb_w_r[] = $n;
            	                    }
            	                    if (in_array($n, explode(',', $item['mb_m_1']))) {
            	                        $mb_m_r[] = $n;
            	                    }
            	                    if (in_array($n, explode(',', $item['mb_y_1']))) {
            	                        $mb_y_r[] = $n;
            	                    }
            	                }
            	            }
            	        }
            	        
            	        $this->db->where('id', $item['id'])->update('lottery_forecast', ['mb_o_r' => implode(',', $mb_o_r), 'mb_w_r' => implode(',', $mb_w_r), 'mb_m_r' => implode(',', $mb_m_r), 'mb_y_r' => implode(',', $mb_y_r)]);
            	        $item['mb_o_r'] = implode(',', $mb_o_r);
            	        $item['mb_w_r'] = implode(',', $mb_w_r);
            	        $item['mb_m_r'] = implode(',', $mb_m_r);
            	        $item['mb_y_r'] = implode(',', $mb_y_r);
            	    }
                }
                
                $data[] = $item;
            }
        }
        
        $this->render('lottery/all', ['data' => $data]);
    }
    
    public function combo()
	{
	    set_time_limit(0);
	    
	    $area = isset($_GET['area']) && $_GET['area'] ? $_GET['area'] : 'MN';
	    $row_num = isset($_GET['row_num']) && $_GET['row_num'] ? $_GET['row_num'] : 30;
	    $position = isset($_GET['position']) && $_GET['position'] ? $_GET['position'] : '1,2';
	    $last_date = isset($_GET['last_date']) && $_GET['last_date'] ? $_GET['last_date'] : date('Y-m-d');
	    
	    $this->db->select('lottery_data.import_date, lottery_data.numbers, lottery_channel.position')->from('lottery_data');
	    $this->db->join('lottery_channel', 'lottery_channel.id = lottery_data.lottery_channel_id');
	    $this->db->where('lottery_data.import_date <= "' . $last_date . '"', null);
	    $this->db->where('lottery_channel.channel_area', $area);
	    $this->db->where('lottery_channel.position IN (' . $position . ')', null);
        $this->db->order_by('import_date DESC');
        $this->db->limit($row_num);
        $query = $this->db->get();
	    $data = $query->result();
	    
	    $result = [];
	    $date_data = [];
	    $last_date = null;
	    foreach ($data as $row) {
	        if (!$last_date) {
	            $last_date = $row->import_date;
	        }
	        $numbers = explode(' ', $row->numbers);
	        foreach ($numbers as $number) {
	            $date_data[$row->import_date][] = $number%100;
	        }
	    }
	    
	    $last_date_numbers = $date_data[$last_date];
	    sort($last_date_numbers);
	    unset($date_data[$last_date]);
	    
	    echo '<pre>';
	    //print_r($date_data);
        foreach ($last_date_numbers as $n1) {
	        for ($n2 = $n1 + 1; $n2 <= 99; $n2 ++) {
	            for ($n3 = $n2 + 1; $n3 <= 99; $n3 ++) {
	                for ($n4 = $n3 + 1; $n4 <= 99; $n4 ++) {
    	                //for ($n5 = $n4 + 1; $n5 <= 99; $n5 ++) {
    	                    if ($n1 < $n2 && $n2 < $n3 && $n3 < $n4) {
        	                    $yes = true;
        	                    foreach ($date_data as $date => $numbers) {
            	                    if (count(array_intersect([$n1, $n2, $n3, $n4], $numbers)) < 2) {
            	                        $yes = false;
            	                        break;
            	                    }
        	                    }
        	                    if ($yes) {
        	                        print_r('[' . $n1 . ',' . $n2 . ',' . $n3 . ',' . $n4 . '],');
        	                    }
    	                    }
    	                //}
    	                //die;
	                }
	            }
	        }
	    }
	    
	    //$this->render('lottery/positions', ['channels' => $channels, 'channel_id' => $channel_id, 'data' => $result]);
	}
    
	public function positions()
	{
	    set_time_limit(0);
	    
	    $channel_id = isset($_GET['channel_id']) && $_GET['channel_id'] ? $_GET['channel_id'] : 1;
	    $row_num = isset($_GET['row_num']) && $_GET['row_num'] ? $_GET['row_num'] : 30;
	    
	    $this->db->select('import_date, numbers, potential_numbers')->from('lottery_data');
	    if ($channel_id == 5 || $channel_id == 21) {
            $this->db->where('lottery_channel_id IN (5,21)', null);
        } else if ($channel_id == 32 || $channel_id == 39) {
            $this->db->where('lottery_channel_id IN (32,39)', null);
        } else if ($channel_id == 25 || $channel_id == 33) {
            $this->db->where('lottery_channel_id IN (25,33)', null);
        } else if ($channel_id == 27 || $channel_id == 29) {
            $this->db->where('lottery_channel_id IN (27,29)', null);
        } else {
            $this->db->where('lottery_channel_id', $channel_id);
        }
        $this->db->order_by('import_date DESC');
        $this->db->limit($row_num);
        $query = $this->db->get();
	    $data = $query->result();
	    
	    $result = [];
	    foreach ($data as $row) {
	        $numbers = explode(' ', $row->numbers);
	        $digits = str_split(str_replace(' ', '', $row->numbers));
	        
	        $result[$row->import_date] = [
	            'numbers' => [],
	            'all_digits' => [0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0, 9 => 0],
	            'matched_digits' => [0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0, 9 => 0]
            ];
	        
	        foreach ($numbers as $number) {
	            $t = $number%100;
	            $result[$row->import_date]['numbers'][] = $t;
	            
	            if ($t == 0) {
	                $result[$row->import_date]['matched_digits'][0] += 2;
	            } else {
	                $result[$row->import_date]['matched_digits'][$t%10] += 1;
	                $result[$row->import_date]['matched_digits'][floor($t/10)] += 1;
	            }
	            
	        }
	        foreach ($digits as $digit) {
	            $result[$row->import_date]['all_digits'][$digit] += 1;
	        }
	    }
	    
	    $query = $this->db->select('id, channel_name, week_day')->from('lottery_channel')->get();
	    $channels = $query->result();
	    
	    print_r($result);
	    
	    //$this->render('lottery/positions', ['channels' => $channels, 'channel_id' => $channel_id, 'data' => $result]);
	}
    
    public function list()
    {
        $this->db->select('import_date, numbers')->from('lottery_data');
        if (in_array($_GET['id'], [5, 21])) {
            $this->db->where('lottery_channel_id IN (5, 21)', null);
        } else if (in_array($_GET['id'], [27, 29])) {
            $this->db->where('lottery_channel_id IN (27, 29)', null);
        } else if (in_array($_GET['id'], [25, 33])) {
            $this->db->where('lottery_channel_id IN (25, 33)', null);
        } else if (in_array($_GET['id'], [32, 39])) {
            $this->db->where('lottery_channel_id IN (32, 39)', null);
        } else {
            $this->db->where('lottery_channel_id', isset($_GET['id']) ? $_GET['id'] : 1);
        }
        $this->db->limit(52)->order_by('import_date DESC');
        $query = $this->db->get();
        $result = $query->result();
        
        $data = [0 => [], 1 => [], 2 => [], 3 => [], 4 => [], 5 => [], 6 => [], 7 => [], 8 => [], 9 => []];
        $data2 = [0 => [], 1 => [], 2 => [], 3 => [], 4 => [], 5 => [], 6 => [], 7 => [], 8 => [], 9 => []];
        
        $last_2 = [];
        if ($result) {
            foreach ($result as $item) {
                $numbers = str_split($item->numbers);
                
                $nrs = [0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0, 9 => 0];
                foreach ($numbers as $number) {
                    if (trim($number) != '') {
                        $nrs[$number]++;
                    }
                }
                foreach ($nrs as $key => $count) {
                    $data[$key][] = ['y' => $count, 'label' => $item->import_date];
                }
                
                $last_2[$item->import_date] = ['chart' => [], 'numbers' => []];
                $numbers = explode(' ', $item->numbers);
                $nrs = [0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0, 9 => 0];
                foreach ($numbers as $number) {
                    if (trim($number) != '') {
                        $number = $number%100;
                        $last_2[$item->import_date]['numbers'][] = $number;
                        $nrs[$number%10]++;
                        $nrs[floor($number/10)]++;
                    }
                }
                foreach ($nrs as $key => $count) {
                    $last_2[$item->import_date]['chart'][] = ['y' => $count, 'label' => $key];
                    $data2[$key][] = ['y' => $count, 'label' => $item->import_date];
                }
            }
        }
        
        $this->render('lottery/list', ['data' => $data, 'data2' => $data2, 'last2' => $last_2]);
    }

	public function index()
	{
	    set_time_limit(0);
	    
	    $channel_id = isset($_GET['channel_id']) && $_GET['channel_id'] ? $_GET['channel_id'] : 1;
	    $row_num = isset($_GET['row_num']) && $_GET['row_num'] ? $_GET['row_num'] : 30;
	    $digit_num = isset($_GET['digit_num']) && $_GET['digit_num'] ? $_GET['digit_num'] : 2;
	    
	    $potential_numbers = $this->get_statistic($channel_id, $row_num, $digit_num);
	    
	    $this->db->select('import_date, numbers, potential_numbers')->from('lottery_data');
	    if ($channel_id == 5 || $channel_id == 21) {
            $this->db->where('lottery_channel_id IN (5,21)', null);
        } else if ($channel_id == 32 || $channel_id == 39) {
            $this->db->where('lottery_channel_id IN (32,39)', null);
        } else if ($channel_id == 25 || $channel_id == 33) {
            $this->db->where('lottery_channel_id IN (25,33)', null);
        } else if ($channel_id == 27 || $channel_id == 29) {
            $this->db->where('lottery_channel_id IN (27,29)', null);
        } else {
            $this->db->where('lottery_channel_id', $channel_id);
        }
        $this->db->order_by('import_date DESC');
        //$this->db->limit($row_num);
        $query = $this->db->get();
	    $result = $query->result();
	    
	    $date_data = [];
	    if ($result) {
	        foreach ($result as $item) {
	            $date_data[$item->import_date] = $item;
	        }
	    }
	    sort($date_data);
	    
	    $data = [];
        $current_pn = null;
        
        foreach ($date_data as $item) {
            $numbers = explode(' ', $item->numbers);
            $count = 0;
            $invert_count = 0;
            
            if ($current_pn) {
                $invert_current_pn = [];
                foreach ($current_pn as $pn) {
                    $invert_pn = 10*($pn%10) + intdiv($pn, 10);
                    $invert_current_pn[] = $invert_pn;
                    foreach ($numbers as $number) {
                        if ($number && $pn == $number%100) {
                            $count ++;
                        }
                        if ($number && $invert_pn == $number%100) {
                            $invert_count ++;
                        }
                    }
                }
                
                $data[$item->import_date] = [
                    'import_date' => $item->import_date, 'potential_numbers' => $current_pn ? implode(',', $current_pn) : '',
                    'invert_numbers' => $invert_current_pn ? implode(',', $invert_current_pn) : '', 'invert_count' => $invert_count,
                    'a' => $numbers[0], 'b' => $numbers[count($numbers) - 1], 'count' => $count
                ];
            }
            
            $current_pn = explode(',', $item->potential_numbers);
            $current_pn = $current_pn ? array_slice($current_pn, 0, 5) : [];
            sort($current_pn);
        }
        
        $data['Next'] = ['import_date' => 'Next', 'potential_numbers' => $current_pn ? implode(',', $current_pn) : '', 'invert_numbers' => '', 'invert_count' => '', 'a' => '', 'b' => '', 'count' => 0];
   
	    
	    $query = $this->db->select('id, channel_name, week_day')->from('lottery_channel')->get();
	    $channels = $query->result();
	    
	    $this->render('lottery/index', ['channels' => $channels, 'channel_id' => $channel_id, 'data' => array_reverse($data)]);
	}
	
	public function test()
	{
	    set_time_limit(0);
	    
	    $data = [];
	    $fields = [];
	    for ($i=0; $i<100; $i++) {
            $fields[] = ($i < 10 ? ('0' . $i) : $i);
        }
        
	    for ($i = 0; $i <= 30; $i++) {
	        $date = date('Y-m-d', strtotime("-" . $i . " days"));
	        $previous_date = date('Y-m-d', strtotime("-" . ($i + 1) . " days"));
	        $data[$date] = 0;
	        
	        $query = $this->db->select('*')->from('lottery_statistic')->where('lottery_channel_id', 1)->where('view_date', $previous_date)->get();
    	    $mb_rows = $query->result_array();
    	    $mb_q = [];
    	    foreach ($mb_rows as $mb_row) {
    	        foreach ($fields as $field) {
    	            if (!isset($mb_q[$field])) {
    	                $mb_q[$field] = $mb_row['number_' . $field] ? $mb_row['number_' . $field] : 0;
    	            } else {
    	                $mb_q[$field] += ($mb_row['number_' . $field] ? $mb_row['number_' . $field] : 0);
    	            }
    	        }
    	    }
    	    
    	    $this->db->select('lottery_statistic.*')->from('lottery_statistic')->join('lottery_channel', 'lottery_channel.id = lottery_statistic.lottery_channel_id');
    	    $query = $this->db->where('lottery_channel.channel_area', 'MN')->where('position <= 2', null)->where('lottery_statistic.view_date', $date)->get();
    	    $mn_rows = $query->result_array();
    	    $mn = [];
    	    foreach ($mn_rows as $mn_row) {
    	        foreach ($fields as $field) {
    	            if (!isset($mn[$field])) {
    	                $mn[$field] = $mn_row['number_' . $field] ? $mn_row['number_' . $field] : 0;
    	            } else {
    	                $mn[$field] += ($mn_row['number_' . $field] ? $mn_row['number_' . $field] : 0);
    	            }
    	        }
    	    }
    	    
    	    foreach ($fields as $field) {
    	        if ($mb_q[$field]) {
    	            $data[$date] += isset($mn[$field]) ? $mn[$field] : 0;
    	        }
    	    }
	    }
	    echo '<pre>';
	    print_r($data);
	}

	public function area()
	{
	    set_time_limit(0);
	    $date = isset($_GET['date']) && $_GET['date'] ? date('Y-m-d', strtotime($_GET['date'])) : date('Y-m-d');
	    $previous_date_1 = date('Y-m-d', strtotime($date . "-1 days"));
	    $previous_date_2 = date('Y-m-d', strtotime($date . "-2 days"));
	    
	    $fields = [];
	    for ($i=0; $i<100; $i++) {
            $fields[] = ($i < 10 ? ('0' . $i) : $i);
        }
        
        $this->db->select('lottery_statistic.*')->from('lottery_statistic')->join('lottery_channel', 'lottery_channel.id = lottery_statistic.lottery_channel_id');
	    $query = $this->db->where('lottery_channel.channel_area', 'MN')->where('position <= 2', null)->where('lottery_statistic.view_date', $previous_date_2)->get();
	    $mn_rows = $query->result_array();
	    $mn_2 = [];
	    foreach ($mn_rows as $mn_row) {
	        foreach ($fields as $field) {
	            if (!isset($mn_2[$field])) {
	                $mn_2[$field] = $mn_row['number_' . $field] ? $mn_row['number_' . $field] : 0;
	            } else {
	                $mn_2[$field] += ($mn_row['number_' . $field] ? $mn_row['number_' . $field] : 0);
	            }
	        }
	    }
	    
	    $this->db->select('lottery_statistic.*')->from('lottery_statistic')->join('lottery_channel', 'lottery_channel.id = lottery_statistic.lottery_channel_id');
	    $query = $this->db->where('lottery_channel.channel_area', 'MT')->where('position <= 2', null)->where('lottery_statistic.view_date', $previous_date_2)->get();
	    $mt_rows = $query->result_array();
	    $mt_2 = [];
	    foreach ($mt_rows as $mt_row) {
	        foreach ($fields as $field) {
	            if (!isset($mt_2[$field])) {
	                $mt_2[$field] = $mt_row['number_' . $field] ? $mt_row['number_' . $field] : 0;
	            } else {
	                $mt_2[$field] += ($mt_row['number_' . $field] ? $mt_row['number_' . $field] : 0);
	            }
	        }
	    }
	    
	    $query = $this->db->select('*')->from('lottery_statistic')->where('lottery_channel_id', 1)->where('view_date', $previous_date_2)->get();
	    $mb_rows = $query->result_array();
	    $mb_2 = [];
	    foreach ($mb_rows as $mb_row) {
	        foreach ($fields as $field) {
	            if (!isset($mb_2[$field])) {
	                $mb_2[$field] = $mb_row['number_' . $field] ? $mb_row['number_' . $field] : 0;
	            } else {
	                $mb_2[$field] += ($mb_row['number_' . $field] ? $mb_row['number_' . $field] : 0);
	            }
	        }
	    }
	    
	    $this->db->select('lottery_statistic.*')->from('lottery_statistic')->join('lottery_channel', 'lottery_channel.id = lottery_statistic.lottery_channel_id');
	    $query = $this->db->where('lottery_channel.channel_area', 'MN')->where('position <= 2', null)->where('lottery_statistic.view_date', $previous_date_1)->get();
	    $mn_rows = $query->result_array();
	    $mn_1 = [];
	    foreach ($mn_rows as $mn_row) {
	        foreach ($fields as $field) {
	            if (!isset($mn_1[$field])) {
	                $mn_1[$field] = $mn_row['number_' . $field] ? $mn_row['number_' . $field] : 0;
	            } else {
	                $mn_1[$field] += ($mn_row['number_' . $field] ? $mn_row['number_' . $field] : 0);
	            }
	        }
	    }
	    
	    $this->db->select('lottery_statistic.*')->from('lottery_statistic')->join('lottery_channel', 'lottery_channel.id = lottery_statistic.lottery_channel_id');
	    $query = $this->db->where('lottery_channel.channel_area', 'MT')->where('position <= 2', null)->where('lottery_statistic.view_date', $previous_date_1)->get();
	    $mt_rows = $query->result_array();
	    $mt_1 = [];
	    foreach ($mt_rows as $mt_row) {
	        foreach ($fields as $field) {
	            if (!isset($mt_1[$field])) {
	                $mt_1[$field] = $mt_row['number_' . $field] ? $mt_row['number_' . $field] : 0;
	            } else {
	                $mt_1[$field] += ($mt_row['number_' . $field] ? $mt_row['number_' . $field] : 0);
	            }
	        }
	    }
	    
	    $query = $this->db->select('*')->from('lottery_statistic')->where('lottery_channel_id', 1)->where('view_date', $previous_date_1)->get();
	    $mb_rows = $query->result_array();
	    $mb_1 = [];
	    foreach ($mb_rows as $mb_row) {
	        foreach ($fields as $field) {
	            if (!isset($mb_1[$field])) {
	                $mb_1[$field] = $mb_row['number_' . $field] ? $mb_row['number_' . $field] : 0;
	            } else {
	                $mb_1[$field] += ($mb_row['number_' . $field] ? $mb_row['number_' . $field] : 0);
	            }
	        }
	    }
	    
	    $this->db->select('lottery_statistic.*')->from('lottery_statistic')->join('lottery_channel', 'lottery_channel.id = lottery_statistic.lottery_channel_id');
	    $query = $this->db->where('lottery_channel.channel_area', 'MN')->where('position <= 2', null)->where('lottery_statistic.view_date', $date)->get();
	    $mn_rows = $query->result_array();
	    $mn = [];
	    foreach ($mn_rows as $mn_row) {
	        foreach ($fields as $field) {
	            if (!isset($mn[$field])) {
	                $mn[$field] = $mn_row['number_' . $field] ? $mn_row['number_' . $field] : 0;
	            } else {
	                $mn[$field] += ($mn_row['number_' . $field] ? $mn_row['number_' . $field] : 0);
	            }
	        }
	    }
	    
	    $this->db->select('lottery_statistic.*')->from('lottery_statistic')->join('lottery_channel', 'lottery_channel.id = lottery_statistic.lottery_channel_id');
	    $query = $this->db->where('lottery_channel.channel_area', 'MT')->where('position <= 2', null)->where('lottery_statistic.view_date', $date)->get();
	    $mt_rows = $query->result_array();
	    $mt = [];
	    foreach ($mt_rows as $mt_row) {
	        foreach ($fields as $field) {
	            if (!isset($mt[$field])) {
	                $mt[$field] = $mt_row['number_' . $field] ? $mt_row['number_' . $field] : 0;
	            } else {
	                $mt[$field] += ($mt_row['number_' . $field] ? $mt_row['number_' . $field] : 0);
	            }
	        }
	    }
	    
	    $query = $this->db->select('*')->from('lottery_statistic')->where('lottery_channel_id', 1)->where('view_date', $date)->get();
	    $mb_rows = $query->result_array();
	    $mb = [];
	    foreach ($mb_rows as $mb_row) {
	        foreach ($fields as $field) {
	            if (!isset($mb[$field])) {
	                $mb[$field] = $mb_row['number_' . $field] ? $mb_row['number_' . $field] : 0;
	            } else {
	                $mb[$field] += ($mb_row['number_' . $field] ? $mb_row['number_' . $field] : 0);
	            }
	        }
	    }
	    
	    $this->render('lottery/area', ['fields' => $fields, 'mn_1' => $mn_1, 'mt_1' => $mt_1, 'mb_1' => $mb_1, 'mn_2' => $mn_2, 'mt_2' => $mt_2, 'mb_2' => $mb_2, 'mn' => $mn, 'mt' => $mt, 'mb' => $mb]);
	}
	
	public function percentage()
	{
	    set_time_limit(0);
	    
	    $channel_area = isset($_GET['c']) && $_GET['c'] ? $_GET['c'] : 'MN';
	    $week_day = isset($_GET['d']) && $_GET['d'] ? $_GET['d'] : 1;
	    
	    if ($channel_area != 'MB') {
    	    $this->db->select('lottery_data.lottery_channel_id, lottery_data.import_date, lottery_data.numbers, lottery_channel.position')->from('lottery_data');
    	    $this->db->join('lottery_channel', 'lottery_channel.id = lottery_data.lottery_channel_id', 'INNER');
    	    $this->db->where('lottery_channel.channel_area', $channel_area);
            $this->db->where('lottery_channel.week_day', $week_day);
    	    $this->db->where('lottery_channel.position IN (1,2)', null);
            $this->db->order_by('lottery_data.import_date, lottery_channel.position');
            $query = $this->db->get();
    	    $rows = $query->result();
    	    
    	    $weekly_data = [];
    	    if ($rows) {
    	        foreach ($rows as $row) {
    	            $numbers = explode(' ', $row->numbers);
    	            
    	            foreach ($numbers as $number) {
    	                if ($number) {
    	                    if (!isset($weekly_data[$number%100])) {
            	                $weekly_data[$number%100] = ['number' => $number%100, 'count' => 0, 'percentage' => 0];
            	            }
    	                    $weekly_data[$number%100]['count'] ++;
    	                }
    	            }
    	        }
    	    }
    	    foreach ($weekly_data as $num => $weekly_item) {
    	        $weekly_data[$num]['percentage'] = number_format(($weekly_item['count']/count($rows))*100, 2) . '%';
    	    }
    	    usort($weekly_data, function($a, $b) {
    	        return $a['count'] <= $b['count'];
    	    });
    	    echo '<pre>';
    	    print_r($weekly_data);
	    }
	    
	    $this->db->select('lottery_data.lottery_channel_id, lottery_data.import_date, lottery_data.numbers, lottery_channel.position')->from('lottery_data');
	    $this->db->join('lottery_channel', 'lottery_channel.id = lottery_data.lottery_channel_id', 'INNER');
	    $this->db->where('lottery_channel.channel_area', $channel_area);
	    $this->db->where('lottery_channel.position IN (1,2)', null);
        $this->db->order_by('lottery_data.import_date, lottery_channel.position');
        $query = $this->db->get();
	    $rows = $query->result();
	    
	    $daily_data = [];
	    if ($rows) {
	        foreach ($rows as $row) {
	            $numbers = explode(' ', $row->numbers);
	            
	            foreach ($numbers as $number) {
	                if ($number) {
	                    if (!isset($daily_data[$number%100])) {
        	                $daily_data[$number%100] = ['number' => $number%100, 'count' => 0, 'percentage' => 0];
        	            }
	                    $daily_data[$number%100]['count'] ++;
	                }
	            }
	        }
	    }
	    foreach ($daily_data as $num => $daily_item) {
	        $daily_data[$num]['percentage'] = number_format(($daily_item['count']/count($rows))*100, 2) . '%';
	    }
	    usort($daily_data, function($a, $b) {
	        return $a['count'] <= $b['count'];
	    });
	    
	    echo '<pre>';
	    print_r($daily_data);
	}
	
	public function d2d()
	{
	    set_time_limit(0);
	    
	    $channel_area = isset($_GET['c']) && $_GET['c'] ? $_GET['c'] : 'MN';
	    $to_date = isset($_GET['d']) && $_GET['d'] ? date('Y-m-d', strtotime($_GET['d'])) : date('Y-m-d');
	    $from_date = date('Y-m-d', strtotime($to_date . "-30 days"));
	    
	    $this->db->select('lottery_data.lottery_channel_id, lottery_data.import_date, lottery_data.numbers, lottery_channel.position')->from('lottery_data');
	    $this->db->join('lottery_channel', 'lottery_channel.id = lottery_data.lottery_channel_id', 'INNER');
	    $this->db->where('lottery_channel.channel_area', $channel_area);
	    $this->db->where('lottery_channel.position IN (1,2)', null);
	    $this->db->where('(lottery_data.import_date >= "' . $from_date . '" AND lottery_data.import_date <= "' . $to_date . '")');
        $this->db->order_by('lottery_data.import_date, lottery_channel.position');
        $query = $this->db->get();
        
	    $rows = $query->result();
	    $date_data = [];
	    if ($rows) {
	        foreach ($rows as $row) {
	            $numbers = explode(' ', $row->numbers);
	            if (!isset($date_data[$row->import_date])) {
	                $date_data[$row->import_date] = ['numbers' => [], 'from_yesterday' => []];
	            }
	            
	            foreach ($numbers as $number) {
	                if ($number) {
	                    $date_data[$row->import_date]['numbers'][] = $number%100;
	                }
	            }
	            
	            $yesterday = date('Y-m-d', strtotime(' -1 days', strtotime($row->import_date)));
	            
	            if (isset($date_data[$yesterday])) {
	                $date_data[$row->import_date]['from_yesterday'] = [];
	                //$date_data[$row->import_date]['forecast'] = [];
	                //$date_data[$row->import_date]['from_forecast'] = [];
	                
	                foreach ($date_data[$row->import_date]['numbers'] as $today_index => $today_num) {
	                    $found = 0;
	                    foreach ($date_data[$yesterday]['numbers'] as $yesterday_index => $yesterday_num) {
	                        if ($yesterday_num == $today_num) {
	                            $date_data[$row->import_date]['from_yesterday'][$yesterday_index] = $yesterday_num;
	                            $found = 1;
	                        }
	                    }
	                    /*if (!$found) {
	                        $date_data[$row->import_date]['forecast'][$today_num] = $today_num;
	                    }
	                    
	                    foreach ($date_data[$yesterday]['forecast'] as $forecast_num) {
	                        if ($forecast_num == $today_num) {
	                            $date_data[$row->import_date]['from_forecast'][$forecast_num] = $forecast_num;
	                        }
	                    }*/
	                }
	                
	                ksort($date_data[$row->import_date]['from_yesterday']);
	                //ksort($date_data[$row->import_date]['forecast']);
	                //ksort($date_data[$row->import_date]['from_forecast']);
	            }
	        }
	    }
	    
	    $pos_data = [];
	    $numbers = [];
	    foreach ($date_data as $date => $from_y) {
	        if ($from_y['from_yesterday']) {
	            foreach ($from_y['from_yesterday'] as $pos => $num) {
	                if (isset($pos_data[$pos])) {
	                    $pos_data[$pos] += 1;
	                } else {
	                    $pos_data[$pos] = 1;
	                }
	            }
	        }
	        $numbers = $date_data[$date]['numbers'];
            unset($date_data[$date]['numbers']);
	    }
	    
	    echo '<pre>';
	    print_r($date_data);
	    
	    echo '<pre>';
	    print_r($numbers);
	    print_r($pos_data);
	}
	
	public function forecastn()
	{
	    set_time_limit(0);
	    
	    $channel_area = isset($_GET['c']) && $_GET['c'] ? $_GET['c'] : 'MN';
	    $week_day = isset($_GET['d']) && $_GET['d'] ? $_GET['d'] : (date('N') + 1);
	    $row_num = isset($_GET['r']) ? ($_GET['r'] == 'y' ? 730 : ($_GET['r'] == 'm' ? 60 : 14)) : 14;
	    
	    $this->db->select('lottery_data.lottery_channel_id, lottery_data.import_date, lottery_data.numbers, lottery_channel.position')->from('lottery_data');
	    $this->db->join('lottery_channel', 'lottery_channel.id = lottery_data.lottery_channel_id', 'INNER');
	    $this->db->where('lottery_channel.channel_area', $channel_area);
	    if ($channel_area != 'MB') {
	        $this->db->where('lottery_channel.week_day', $week_day);
	    }
	    $this->db->where('lottery_channel.position IN (1,2)', null);
        $this->db->order_by('lottery_data.import_date DESC, lottery_channel.position');
        $this->db->limit($row_num);
        $query = $this->db->get();
	    $rows = $query->result();
	    
	    $date_data = [];
	    if ($rows) {
	        foreach ($rows as $row) {
	            if (!isset($date_data[$row->import_date])) {
	                $date_data[$row->import_date] = ['last2' => [], 'all' => [], 'forecast' => []];
	            }
	            
	            $nums = explode(' ', $row->numbers);
	            foreach ($nums as $num) {
	                $dgs = str_split($num%100, 1);
	                foreach ($dgs as $dg) {
    	                if ($dg != ' ' && $dg != '') {
    	                    if (!isset($date_data[$row->import_date]['last2'][$dg])) {
    	                        $date_data[$row->import_date]['last2'][$dg] = 0;
    	                    }
    	                    $date_data[$row->import_date]['last2'][$dg] += 1;
    	                }
    	            }
	            }
	            
	            $numbers = str_split($row->numbers, 1);
	            
	            foreach ($numbers as $number) {
	                if ($number != ' ' && $number != '') {
	                    if (!isset($date_data[$row->import_date]['all'][$number])) {
	                        $date_data[$row->import_date]['all'][$number] = 0;
	                    }
	                    $date_data[$row->import_date]['all'][$number] += 1;
	                }
	            }
	        }
	    }
	    
	    foreach ($date_data as $date => $data) {
	        
	    }
	    
	    echo '<pre>';
	    print_r($date_data);
	}
	
	public function forecast()
	{
	    set_time_limit(0);
	    
	    $channel_area = isset($_GET['c']) && $_GET['c'] ? $_GET['c'] : 'MN';
	    if (isset($_GET['date']) && $_GET['date']) {
	        $week_day = date('w', strtotime($_GET['date'])) + 1;
	    } else {
	        $week_day = isset($_GET['d']) && $_GET['d'] ? $_GET['d'] : (date('w') + 1);
	    }
	    if ($channel_area == 'MB') {
	        $row_num = isset($_GET['r']) ? ($_GET['r'] == 'y' ? 365 : ($_GET['r'] == 'm' ? 30 : 7)) : 7;
	    } else {
	        $row_num = isset($_GET['r']) ? ($_GET['r'] == 'y' ? 730 : ($_GET['r'] == 'm' ? 60 : 14)) : 14;
	    }
	    
	    print_r($this->run_forecast($channel_area, $week_day, $row_num));
	}
	
	
	
	public function day_average()
	{
	    set_time_limit(0);
	    
	    $channel_area = isset($_GET['c']) && $_GET['c'] ? $_GET['c'] : 'MN';
	    $row_num = isset($_GET['n']) && $_GET['n'] ? $_GET['n'] : '';
	    
	    $result = [];
	    
	    $this->db->select('lottery_data.lottery_channel_id, lottery_data.import_date, lottery_data.numbers, lottery_channel.position')->from('lottery_data');
	    $this->db->join('lottery_channel', 'lottery_channel.id = lottery_data.lottery_channel_id', 'INNER');
	    $this->db->where('lottery_channel.channel_area', $channel_area);
	    if (isset($_GET['date']) && $_GET['date']) {
	        $this->db->where('lottery_data.import_date < "' . date('Y-m-d', strtotime($_GET['date'])) . '"', null);
	    }
	    $this->db->where('lottery_channel.position IN (1,2)', null);
        $this->db->order_by('lottery_data.import_date DESC, lottery_channel.position');
        if ($row_num) {
            $this->db->limit($row_num);
        }
        $query = $this->db->get();
	    $rows = $query->result();
	    
	    for ($i=0; $i<=99; $i++) {
	        $result[$i] = [0 => [], 1 => [], 2 => []];
	    }
	    
	    $last_date = null;
	    if ($rows) {
	        foreach ($rows as $row) {
	            $numbers = explode(' ', $row->numbers);
	            foreach ($numbers as $number) {
	                if ($number != '') {
	                    $number = intval($number)%100;
	                    
	                    $result[$number][0][$row->import_date] = $row->import_date;
	                    $result[$number][$row->position][$row->import_date] = $row->import_date;
	                }
	            }
	            
	            $last_date = $row->import_date;
	        }
	    }
	    
	    //echo '<pre>';
	    //print_r($result);
	    
	    $average = [];
	    foreach ($result as $number => $channel) {
	        $number_info = ['number' => $number, 'days' => count($channel[0]), 'average' => abs(strtotime($last_date) - strtotime(date('Y-m-d')))/(86400 * count($channel[0]))];
	        foreach ($channel as $pos => $dates) {
    	        $gan = [];
    	        $date1 = null;
    	        foreach ($dates as $date) {
    	            if ($date1) {
                        $gan[$date] = abs(strtotime($date) - strtotime($date1))/86400;
    	            } else {
    	                $gan[$date] = abs(strtotime($date) - strtotime(date('Y-m-d')))/86400;
    	            }
    	            if ($date1 != $date) {
    	                $date1 = $date;
    	            }
    	        }
    	        
    	        if (isset($_GET['pos']) && $_GET['pos'] == $pos) {
    	        $number_info[$pos] = $gan;
    	        }
	        }
	        
	        $average[] = $number_info;
	    }
	    
	    usort($average, function($a, $b) {
	        return $a['average'] >= $b['average'];
	    });
	    
	    echo '<pre>';
	    print_r($average);
	}
	
	private function run_forecast($channel_area, $week_day, $row_num)
	{
	    set_time_limit(0);
	    $result = [];
	    
	    $this->db->select('lottery_data.lottery_channel_id, lottery_data.import_date, lottery_data.numbers, lottery_channel.position')->from('lottery_data');
	    $this->db->join('lottery_channel', 'lottery_channel.id = lottery_data.lottery_channel_id', 'INNER');
	    $this->db->where('lottery_channel.channel_area', $channel_area);
	    if ($channel_area != 'MB') {
	        $this->db->where('lottery_channel.week_day', $week_day);
	    }
	    if (isset($_GET['date']) && $_GET['date']) {
	        $this->db->where('lottery_data.import_date < "' . date('Y-m-d', strtotime($_GET['date'])) . '"', null);
	    }
	    $this->db->where('lottery_channel.position IN (1,2)', null);
        $this->db->order_by('lottery_data.import_date DESC, lottery_channel.position');
        $this->db->limit($row_num);
        $query = $this->db->get();
	    $rows = $query->result();
	    
	    $main_channel_data = [];
	    $sub_channel_data = [];
	    $date_data = [];
	    $last_main_numbers = [];
	    $last_sub_numbers = [];
	    if ($rows) {
	        //print_r($rows);die;
	        foreach ($rows as $row_index => $row) {
	            $numbers = explode(' ', $row->numbers);
	            
	            if ($row_index > 1) {
    	            if ($row->position == 1) {
	                    $main_channel_data[$row->import_date] = str_split(str_replace(' ', '', $row->numbers), 1);
    	            } else if ($row->position == 2) {
	                    $sub_channel_data[$row->import_date] = str_split(str_replace(' ', '', $row->numbers), 1);
    	            }
	            } else {
	                
    	            if ($row->position == 1) {
    	                $last_main_numbers = str_split(str_replace(' ', '', $row->numbers), 1);
    	            } else if ($row->position == 2) {
    	                $last_sub_numbers = str_split(str_replace(' ', '', $row->numbers), 1);
    	            }
	            }
                foreach ($numbers as $number) {
	                if ($number) {
	                    $date_data[$row->import_date][] = $number%100;
	                }
	            }
	        }
	    }
	    
	    $mcd = [];
	    if ($main_channel_data) {
	        foreach ($main_channel_data as $date => $r) {
	            $mcd[] = $r;
	        }
	    }
	    $scd = [];
	    if ($sub_channel_data) {
	        foreach ($sub_channel_data as $date => $r) {
	            $scd[] = $r;
	        }
	    }
	    $dd = [];
	    if ($date_data) {
	        foreach ($date_data as $date => $r) {
	            $dd[] = $r;
	        }
	    }
	    
	    $m_result = [];
	    foreach ($mcd as $index => $mcd_item) {
	        $all_combinations = [];
	        foreach ($mcd_item as $p1 => $d1) {
                foreach ($mcd_item as $p2 => $d2) {
                    if ($p1 < $p2 && !isset($all_combinations[$p1 . '-' . $p2]) && !isset($all_combinations[$p2 . '-' . $p1])) {
                        $all_combinations[$p1 . '-' . $p2] = $d1 . $d2;
                        
                        if (in_array(intval($d1 . $d2), $dd[$index])) {
                            if (isset($m_result[$p1 . '-' . $p2])) {
                                $m_result[$p1 . '-' . $p2]['count'] += 1;
                            } else {
                                $m_result[$p1 . '-' . $p2] = ['number' => $last_main_numbers[$p1] . $last_main_numbers[$p2], 'pos' => $p1 . '-' . $p2, 'count' => 1];
                            }
                        }
                    }
                }
            }
	    }
	    
	    $positions = [];
	    //if ($row_num == 365 || $row_num == 730) {
    	    foreach ($m_result as $pos => $n) {
    	        $positions[$pos] = $n['count'];
    	    }
	    //}
	    
	    usort($m_result, function($a, $b) {
	        return $a['count'] <= $b['count'];
	    });
	    if (isset($_GET['print']) && $_GET['print']) {
	        echo '<pre>MM';
	        //print_r($m_result);
	        foreach ($m_result as $pos_index => $positions) {
	            if ($pos_index < 10) {
        	        print_r($positions);
        	        //echo '<br/>';
	            } else {
	                break;
	            }
	        }
	    }
	    
	    if ($channel_area != 'MB') {
    	    $s_result = [];
    	    foreach ($scd as $index => $scd_item) {
    	        $all_combinations = [];
    	        foreach ($scd_item as $p1 => $d1) {
                    foreach ($scd_item as $p2 => $d2) {
                        if ($p1 < $p2 && !isset($all_combinations[$p1 . '-' . $p2]) && !isset($all_combinations[$p2 . '-' . $p1])) {
                            $all_combinations[$p1 . '-' . $p2] = $d1 . $d2;
                            
                            if (in_array(intval($d1 . $d2), $dd[$index])) {
                                if (isset($s_result[$p1 . '-' . $p2])) {
                                    $s_result[$p1 . '-' . $p2]['count'] += 1;
                                } else {
                                    $s_result[$p1 . '-' . $p2] = ['number' => $last_sub_numbers[$p1] . $last_sub_numbers[$p2], 'pos' => $p1 . '-' . $p2, 'count' => 1];
                                }
                            }
                        }
                    }
                }
    	    }
    	    
    	    //if ($row_num == 365 || $row_num == 730) {
        	    foreach ($s_result as $pos => $n) {
        	        $positions[$pos] = isset($positions[$pos]) ? ($positions[$pos] + $n['count']) : $n['count'];
        	    }
    	    //}
    	    
    	    usort($s_result, function($a, $b) {
    	        return $a['count'] <= $b['count'];
    	    });
    	    if (isset($_GET['print']) && $_GET['print']) {
    	        echo '<pre>MT';
    	        //print_r($s_result);
    	        foreach ($s_result as $pos_index => $positions) {
    	            if ($pos_index < 10) {
            	        print_r($positions);
            	        //echo '<br/>';
    	            } else {
    	                break;
    	            }
    	        }
    	    }
	    }
	    
	    if (!isset($_GET['print']) || !$_GET['print']) {
	    
    	    $result = [];
    	    $r1 = '';
    	    foreach (array_slice($m_result, 0, 5) as $n) {
    	        $r1 .= ($r1 ? ',' : '') . $n['number'];
    	    }
    	    $result[] = $r1;
    	    
    	    if ($channel_area != 'MB') {
        	    $r2 = '';
        	    foreach (array_slice($s_result, 0, 5) as $n) {
        	        $r2 .= ($r2 ? ',' : '') . $n['number'];
        	    }
    	        $result[] = $r2;
    	    }
            
    	    if ($channel_area == 'MB') {
    	        $view_date = isset($_GET['date']) ? date('Y-m-d', strtotime($_GET['date'])) : date('Y-m-d');
                $this->db->select('*')->from('lottery_forecast');
        	    $this->db->where('view_date', $view_date);
                $query = $this->db->get();
                $row = $query->row();
                
                if ($row) {
                    if ($row_num == 30) {
                        $this->db->where('id', $row->id)->update('lottery_forecast', ['mb_m_1' => $result[0]]);
                    } else if ($row_num == 365) {
                        $this->db->where('id', $row->id)->update('lottery_forecast', ['mb_y_1' => $result[0]]);
                    } else {
                        $this->db->where('id', $row->id)->update('lottery_forecast', ['mb_w_1' => $result[0]]);
                    }
                } else {
                    if ($row_num == 30) {
                        $this->db->insert('lottery_forecast', ['view_date' => $view_date, 'mb_m_1' => $result[0]]);
                    } else if ($row_num == 365) {
                        $this->db->insert('lottery_forecast', ['view_date' => $view_date, 'mb_y_1' => $result[0]]);
                    } else {
                        $this->db->insert('lottery_forecast', ['view_date' => $view_date, 'mb_w_1' => $result[0]]);
                    }
                }
    	    } else if ($channel_area == 'MN') {
    	        $cur_dn = date('w') + 1;
    	        if ($week_day < $cur_dn) {
    	            $week_day += 7;
    	        }
    	        $view_date = date('Y-m-d', strtotime(($week_day - $cur_dn) . ' days'));
    	        if (isset($_GET['date']) && $_GET['date']) {
    	            $view_date = date('Y-m-d', strtotime($_GET['date']));
    	        }
    	        
                $this->db->select('*')->from('lottery_forecast');
        	    $this->db->where('view_date', $view_date);
                $query = $this->db->get();
                $row = $query->row();
                
                if ($row) {
                    if ($row_num == 60) {
                        $this->db->where('id', $row->id)->update('lottery_forecast', ['mn_m_1' => $result[0], 'mn_m_2' => $result[1]]);
                    } else if ($row_num == 730) {
                        $this->db->where('id', $row->id)->update('lottery_forecast', ['mn_y_1' => $result[0], 'mn_y_2' => $result[1]]);
                    } else {
                        $this->db->where('id', $row->id)->update('lottery_forecast', ['mn_w_1' => $result[0], 'mn_w_2' => $result[1]]);
                    }
                } else {
                    if ($row_num == 60) {
                        $this->db->insert('lottery_forecast', ['view_date' => $view_date, 'mn_m_1' => $result[0], 'mn_m_2' => $result[1]]);
                    } else if ($row_num == 730) {
                        $this->db->insert('lottery_forecast', ['view_date' => $view_date, 'mn_y_1' => $result[0], 'mn_y_2' => $result[1]]);
                    } else {
                        $this->db->insert('lottery_forecast', ['view_date' => $view_date, 'mn_w_1' => $result[0], 'mn_w_2' => $result[1]]);
                    }
                }
    	    } else if ($channel_area == 'MT') {
    	        $cur_dn = date('w') + 1;
    	        if ($week_day < $cur_dn) {
    	            $week_day += 7;
    	        }
    	        $view_date = date('Y-m-d', strtotime(($week_day - $cur_dn) . ' days'));
    	        if (isset($_GET['date']) && $_GET['date']) {
    	            $view_date = date('Y-m-d', strtotime($_GET['date']));
    	        }
    	        
                $this->db->select('*')->from('lottery_forecast');
        	    $this->db->where('view_date', $view_date);
                $query = $this->db->get();
                $row = $query->row();
                
                if ($row) {
                    if ($row_num == 60) {
                        $this->db->where('id', $row->id)->update('lottery_forecast', ['mt_m_1' => $result[0], 'mt_m_2' => $result[1]]);
                    } else if ($row_num == 730) {
                        $this->db->where('id', $row->id)->update('lottery_forecast', ['mt_y_1' => $result[0], 'mt_y_2' => $result[1]]);
                    } else {
                        $this->db->where('id', $row->id)->update('lottery_forecast', ['mt_w_1' => $result[0], 'mt_w_2' => $result[1]]);
                    }
                } else {
                    if ($row_num == 60) {
                        $this->db->insert('lottery_forecast', ['view_date' => $view_date, 'mt_m_1' => $result[0], 'mt_m_2' => $result[1]]);
                    } else if ($row_num == 730) {
                        $this->db->insert('lottery_forecast', ['view_date' => $view_date, 'mt_y_1' => $result[0], 'mt_y_2' => $result[1]]);
                    } else {
                        $this->db->insert('lottery_forecast', ['view_date' => $view_date, 'mt_w_1' => $result[0], 'mt_w_2' => $result[1]]);
                    }
                }
    	    }
    	    if ($channel_area == 'MN' && $row_num == 730) {
    	        //print_r($positions);
    	    }
    	    return $result;
	    }
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
	            
	            redirect('lottery/add?channel_id=' . $_POST['lottery_channel_id'] . '&date=' . $_POST['import_date']);
	        }
	    }
	    
	    $this->render('lottery/add', ['channels' => $channels]);
	}
	
	public function ab()
	{
	    $daily_data = [];
	    if ($_GET && $_GET['channel_area']) {
	        $this->db->select('import_date, numbers');
	        $this->db->from('lottery_data');
	        $this->db->join('lottery_channel', 'lottery_channel.id = lottery_data.lottery_channel_id');
	        $this->db->where('channel_area', $_GET['channel_area']);
            $this->db->where('position <= 2', null);
            $this->db->order_by('import_date DESC, position ASC');
            $query = $this->db->get();
            
	        $data = $query->result();
	        
	        if ($data) {
	            $array = [];
	            $count = isset($_GET['c']) && $_GET['c'] ? $_GET['c'] : 5;
	            foreach ($data as $item) {
	                if (count($array) == 100 - $count) {
	                    break;
	                }
	                
	                $numbers = explode(' ', rtrim($item->numbers));
	                if ($numbers) {
                       $a = $numbers[0];
                       $b = $numbers[count($numbers) - 1];
                       if (!is_numeric($b)) {
                           $b = $numbers[count($numbers) - 2];
                       }
                       
                       if (!isset($array[$a%100])) {
                            $array[$a%100] = $item->import_date;
                       }
                       if (!isset($array[$b%100])) {
                            $array[$b%100] = $item->import_date;
                       }
                   }
	            }
	            //ksort($array);
	            //echo '<pre>';
	            //print_r($array);
	            
	            $res = [];
	            for ($i = 0; $i < 100; $i ++) {
	                if (!isset($array[$i])) {
	                    $res[] = $i;
	                }
	            }
	            echo '<pre>';
	            print_r($res);die;
	        }
	    }
	    
	    $query = $this->db->select('id, channel_area, channel_name, week_day')->from('lottery_channel')->get();
	    $channels = $query->result();
	    
	    $this->render('lottery/threedigits', ['channels' => $channels, 'daily_data' => $daily_data]);
	}
	
	public function d3()
	{
	    $daily_data = [];
	    if ($_GET && $_GET['channel_area']) {
	        $this->db->select('import_date, numbers');
	        $this->db->from('lottery_data');
	        $this->db->join('lottery_channel', 'lottery_channel.id = lottery_data.lottery_channel_id');
	        $this->db->where('channel_area', $_GET['channel_area']);
            $this->db->where('position <= 2', null);
            if(isset($_GET['d']) && $_GET['d']) {
                $this->db->where('import_date <= "' . date('Y-m-d', strtotime($_GET['d'])) . '"', null);    
            }
            $this->db->order_by('import_date DESC, position ASC');
            $query = $this->db->get();
            
	        $data = $query->result();
	        
	        if ($data) {
	            $array = [];
	            $count = isset($_GET['c']) && $_GET['c'] ? $_GET['c'] : 5;
	            foreach ($data as $item) {
	                if (count($array) >= 1000 - $count) {
	                    break;
	                }
	                
	                $numbers = explode(' ', rtrim($item->numbers));
	                if ($numbers) {
                       foreach ($numbers as $number) {
                           if (!is_numeric($number) || $number < 100) {
                               continue;
                           }
                           $digit_3 = $number % 1000;
                           if (!isset($array[$digit_3])) {
                                $array[$digit_3] = $item->import_date;
                           }
                       }
                   }
	            }
	            //ksort($array);
	            //echo '<pre>';
	            //print_r($array);
	            
	            $res = [];
	            for ($i = 0; $i < 1000; $i ++) {
	                if (!isset($array[$i])) {
	                    $res[] = $i;
	                }
	            }
	            echo '<pre>';
	            print_r($res);die;
	        }
	    }
	    
	    $query = $this->db->select('id, channel_area, channel_name, week_day')->from('lottery_channel')->get();
	    $channels = $query->result();
	    
	    $this->render('lottery/threedigits', ['channels' => $channels, 'daily_data' => $daily_data]);
	}
	
	public function aab()
	{
	    $daily_data = [];
	    if ($_GET && $_GET['channel_area']) {
	        $this->db->select('import_date, numbers');
	        $this->db->from('lottery_data');
	        $this->db->join('lottery_channel', 'lottery_channel.id = lottery_data.lottery_channel_id');
	        $this->db->where('channel_area', $_GET['channel_area']);
	        if (isset($_GET['channel_id']) && $_GET['channel_id']) {
	            $channel_id = $_GET['channel_id'];
	            if ($channel_id == 5 || $channel_id == 21) {
                    $this->db->where('lottery_channel_id IN (5,21)', null);
                } else if ($channel_id == 32 || $channel_id == 39) {
                    $this->db->where('lottery_channel_id IN (32,39)', null);
                } else if ($channel_id == 25 || $channel_id == 33) {
                    $this->db->where('lottery_channel_id IN (25,33)', null);
                } else if ($channel_id == 27 || $channel_id == 29) {
                    $this->db->where('lottery_channel_id IN (27,29)', null);
                } else {
                    $this->db->where('lottery_channel_id', $channel_id);
                }
	        }
            $this->db->where('position <= 2', null);
            $this->db->order_by('import_date DESC, position ASC');
            $query = $this->db->get();
            
	        $data = $query->result();
	        
	        if ($data) {
	            $array = [];
	            foreach ($data as $item) {
	                $numbers = explode(' ', rtrim($item->numbers));
	                if ($numbers) {
                       foreach ($numbers as $number) {
                           if (!is_numeric($number) || $number < 100) {
                               continue;
                           }
                           $digit_3 = $number % 1000;
                           $digit_2 = $number % 100;
                           if (intdiv($digit_3,100) == intdiv($digit_2,10)) {
                                $array[$item->import_date][] = $digit_3;
                           }
                       }
                   }
	            }
	            
	            echo '<pre>';
	            print_r($array);die;
	        }
	    }
	    
	    $query = $this->db->select('id, channel_area, channel_name, week_day')->from('lottery_channel')->get();
	    $channels = $query->result();
	    
	    $this->render('lottery/threedigits', ['channels' => $channels, 'daily_data' => $daily_data]);
	}
	
	public function aba()
	{
	    $daily_data = [];
	    if ($_GET && $_GET['channel_area']) {
	        $this->db->select('import_date, numbers');
	        $this->db->from('lottery_data');
	        $this->db->join('lottery_channel', 'lottery_channel.id = lottery_data.lottery_channel_id');
	        $this->db->where('channel_area', $_GET['channel_area']);
	        if (isset($_GET['channel_id']) && $_GET['channel_id']) {
	            $channel_id = $_GET['channel_id'];
	            if ($channel_id == 5 || $channel_id == 21) {
                    $this->db->where('lottery_channel_id IN (5,21)', null);
                } else if ($channel_id == 32 || $channel_id == 39) {
                    $this->db->where('lottery_channel_id IN (32,39)', null);
                } else if ($channel_id == 25 || $channel_id == 33) {
                    $this->db->where('lottery_channel_id IN (25,33)', null);
                } else if ($channel_id == 27 || $channel_id == 29) {
                    $this->db->where('lottery_channel_id IN (27,29)', null);
                } else {
                    $this->db->where('lottery_channel_id', $channel_id);
                }
	        }
            $this->db->where('position <= 2', null);
            $this->db->order_by('import_date DESC, position ASC');
            $query = $this->db->get();
            
	        $data = $query->result();
	        
	        if ($data) {
	            $array = [];
	            foreach ($data as $item) {
	                $numbers = explode(' ', rtrim($item->numbers));
	                if ($numbers) {
                       foreach ($numbers as $number) {
                           if (!is_numeric($number) || $number < 100) {
                               continue;
                           }
                           $digit_3 = $number % 1000;
                           if (intdiv($digit_3,100) == $digit_3%10) {
                                $array[$item->import_date][] = $digit_3;
                           }
                       }
                   }
	            }
	            
	            echo '<pre>';
	            print_r($array);die;
	        }
	    }
	    
	    $query = $this->db->select('id, channel_area, channel_name, week_day')->from('lottery_channel')->get();
	    $channels = $query->result();
	    
	    $this->render('lottery/threedigits', ['channels' => $channels, 'daily_data' => $daily_data]);
	}
	
	public function statistics()
	{
	    $daily_data = [];
	    $daily_date = [];
	    $weekly_data = [];
	    $weekly_week = [];
	    
	    if ($_GET && $_GET['channel_area']) {
	        if (isset($_GET['lottery_channel_id']) && $_GET['lottery_channel_id']) {
                $arr = explode(',', $_GET['lottery_channel_id']);
                if (count($arr) <= 1) {
        	        if (in_array($_GET['lottery_channel_id'], [5,21,32,39,25,33,27,29])) {
                        $this->db->select('channel_name, 1 AS position, week_day, lottery_statistic.*');
                    } else {
                        $this->db->select('channel_name, position, week_day, lottery_statistic.*');
                    }
                } else {
                    $pos = 'CASE ';
                    foreach ($arr as $index => $c) {
                        $pos .= ' WHEN lottery_channel.id = ' . $c . ' THEN ' . ($index + 1);
                    }
                    $pos .= ' ELSE 1 END AS position';
                    $this->db->select('channel_name, ' . $pos . ', week_day, lottery_statistic.*', false);
                }
	        } else {
	            $this->db->select('channel_name, position, week_day, lottery_statistic.*');
	        }
	        
	        $this->db->from('lottery_statistic');
	        $this->db->join('lottery_channel', 'lottery_channel.id = lottery_statistic.lottery_channel_id');
	        $this->db->where('channel_area', $_GET['channel_area']);
	        if (isset($_GET['lottery_channel_id']) && $_GET['lottery_channel_id']) {
	            if (count($arr) <= 1) {
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
	            } else {
	                $this->db->where('lottery_channel_id IN (' . $_GET['lottery_channel_id'] . ')', null);
	            }
            }
            $this->db->where('lottery_statistic.view_date >= "' . date('Y-m-d', strtotime("-365 days", time())) . '"', null);
            $this->db->where('position <= 2', null);
            $this->db->order_by('lottery_statistic.view_date DESC, position ASC');
            $query = $this->db->get();
            
	        $data = $query->result();
	        
	        if ($data) {
	            foreach ($data as $item) {
	                $daily_date[$item->view_date] = $item->view_date;
	                if (!isset($_GET['lottery_channel_id']) || !$_GET['lottery_channel_id']) {
    	                if (count($weekly_week) < 10) {
    	                    $weekly_week[date('W-y', strtotime($item->view_date))] = date('W-y', strtotime($item->view_date));
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
        	                if (count($weekly_week) <= 10) {
        	                    if (!isset($weekly_data[$col][$item->position][date('W-y', strtotime($item->view_date))])) {
        	                        $weekly_data[$col][$item->position][date('W-y', strtotime($item->view_date))] = ['count' => 0, 'notes' => ''];
        	                    }
        	                    $weekly_data[$col][$item->position][date('W-y', strtotime($item->view_date))]['count'] += ($item->$col_field ? $item->$col_field : 0);
        	                    if ($weekly_data[$col][$item->position][date('W-y', strtotime($item->view_date))]['notes']) {
        	                        $weekly_data[$col][$item->position][date('W-y', strtotime($item->view_date))]['notes'] .= ($item->$col_notes_field ? (',' . $item->$col_notes_field) : '');
    	                        } else {
    	                            $weekly_data[$col][$item->position][date('W-y', strtotime($item->view_date))]['notes'] .= ($item->$col_notes_field ? $item->$col_notes_field : '');
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
        	                if (count($weekly_week) <= 10) {
        	                    if (!isset($weekly_data[$col][1][date('W-y', strtotime($item->view_date))])) {
        	                        $weekly_data[$col][1][date('W-y', strtotime($item->view_date))] = ['count' => 0, 'notes' => ''];
        	                    }
        	                    $weekly_data[$col][1][date('W-y', strtotime($item->view_date))]['count'] += ($item->$col_field ? $item->$col_field : 0);
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
        	                if (count($weekly_week) <= 10) {
        	                    if (!isset($weekly_data[$col][1][date('W-y', strtotime($item->view_date))])) {
        	                        $weekly_data[$col][1][date('W-y', strtotime($item->view_date))] = ['count' => 0, 'notes' => ''];
        	                    }
        	                    $weekly_data[$col][1][date('W-y', strtotime($item->view_date))]['count'] += ($item->$col_field ? $item->$col_field : 0);
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
    	                if (count($weekly_week) <= 10) {
    	                    if (!isset($weekly_data['xx'][1][date('W-y', strtotime($item->view_date))])) {
    	                        $weekly_data['xx'][1][date('W-y', strtotime($item->view_date))] = ['count' => 0, 'notes' => ''];
    	                    }
    	                    $weekly_data['xx'][1][date('W-y', strtotime($item->view_date))]['count'] += ($item->number_xx ? $item->number_xx : 0);
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
	       //->where('import_date', '2022-03-06')->where('lottery_channel_id', 2)
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
                       if (strlen($number) > 2) {
                           if (isset($records[$index]['number_' . ($digit_2 < 10 ? ('0' . $digit_2) : $digit_2) . '_notes'])) {
                               $records[$index]['number_' . ($digit_2 < 10 ? ('0' . $digit_2) : $digit_2) . '_notes'] .= ',' . ($digit_3 < 10 ? ('00' . $digit_3) : ($digit_3 < 100 ? ('0' . $digit_3) : $digit_3));
                           } else {
                               $records[$index]['number_' . ($digit_2 < 10 ? ('0' . $digit_2) : $digit_2) . '_notes'] = ($digit_3 < 10 ? ('00' . $digit_3) : ($digit_3 < 100 ? ('0' . $digit_3) : $digit_3));
                           }
                       }
                       
                       for ($i=0; $i<10; $i++) {
                           if (intdiv($digit_2,10) == $i) {
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
	    
	    $this->db->select('lottery_channel_id, import_date, numbers')->from('lottery_data');
	    if ($channel_ids == 5 || $channel_ids == 21) {
            $this->db->where('lottery_channel_id IN (5,21)', null);
        } else if ($channel_ids == 32 || $channel_ids == 39) {
            $this->db->where('lottery_channel_id IN (32,39)', null);
        } else if ($channel_ids == 25 || $channel_ids == 33) {
            $this->db->where('lottery_channel_id IN (25,33)', null);
        } else if ($channel_ids == 27 || $channel_ids == 29) {
            $this->db->where('lottery_channel_id IN (27,29)', null);
        } else {
            $this->db->where('lottery_channel_id', $channel_ids);
        }
        $this->db->order_by('import_date DESC');
        $this->db->limit($row_num);
        $query = $this->db->get();
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
	                                    if (is_numeric($number) && $number % (pow(10, $digit_num)) == intval($d1 . $d2)) {
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
                if (!isset($data_value['digits'][$pos[0]]) || !isset($data_value['digits'][$pos[1]])) {
                    continue;
                }
                $result_item['numbers'][$data_value['import_date']] = $data_value['digits'][$pos[0]] . $data_value['digits'][$pos[1]];
                if (!isset($potential_numbers[$data_value['import_date']])) {
                    $potential_numbers[$data_value['import_date']] = [];
                }
                $potential_numbers[$data_value['import_date']][] = $data_value['digits'][$pos[0]] . $data_value['digits'][$pos[1]];
            }
        }
        
        if ($potential_numbers) {
            $channel_ids = explode(',', $channel_ids);
            if (count($channel_ids) == 1) {
                foreach ($potential_numbers as $date => $date_potential_numbers) {
                    sort($date_potential_numbers);
                    $this->db->where('lottery_channel_id', $channel_ids[0])->where('import_date', $date)->where('potential_numbers IS NULL', null)->update('lottery_data', ['potential_numbers' => implode(',', $date_potential_numbers)]);
                }
            }
        }
        
        return $potential_numbers;
	}
}