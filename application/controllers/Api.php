<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends My_Controller {
    
    public function __construct()
    {
        parent::__construct();
    }

	public function forecast_mn()
	{
        $query = $this->db->select('*')->from('lottery_forecast')->where('view_date', date('Y-m-d'))->get();
        $result = $query->row_array();
        
        $res = [];
        $res = array_merge($res, explode(',', $result['mn_o_1']));
        $res = array_merge($res, explode(',', $result['mn_o_2']));
        $res = array_merge($res, explode(',', $result['mn_w_1']));
        $res = array_merge($res, explode(',', $result['mn_w_2']));
        $res = array_merge($res, explode(',', $result['mn_m_1']));
        $res = array_merge($res, explode(',', $result['mn_m_2']));
        $res = array_merge($res, explode(',', $result['mn_y_1']));
        $res = array_merge($res, explode(',', $result['mn_y_2']));
        
        $counts = array_count_values($res);

        // Sort counts array in descending order
        arsort($counts);
        
        // Take the top 5 values
        $top5 = array_slice($counts, 0, 5, true);
        
        echo json_encode(array_keys($top5));
	    exit();
	}
	
	public function forecast_mt()
	{
        $query = $this->db->select('*')->from('lottery_forecast')->where('view_date', date('Y-m-d'))->get();
        $result = $query->row_array();
        
        $res = [];
        $res = array_merge($res, explode(',', $result['mt_o_1']));
        $res = array_merge($res, explode(',', $result['mt_o_2']));
        $res = array_merge($res, explode(',', $result['mt_w_1']));
        $res = array_merge($res, explode(',', $result['mt_w_2']));
        $res = array_merge($res, explode(',', $result['mt_m_1']));
        $res = array_merge($res, explode(',', $result['mt_m_2']));
        $res = array_merge($res, explode(',', $result['mt_y_1']));
        $res = array_merge($res, explode(',', $result['mt_y_2']));
        
        $counts = array_count_values($res);

        // Sort counts array in descending order
        arsort($counts);
        
        // Take the top 5 values
        $top5 = array_slice($counts, 0, 5, true);
        
        echo json_encode(array_keys($top5));
	    exit();
	}
	
	public function stations_mn()
	{
	    $query = $this->db->select('id, channel_name, week_day')->from('lottery_channel')->where('channel_area', 'MN')->where('week_day', date('w') + 1)->where('position IN (1,2)', null)->get();
	    $channels = $query->result();
	    
	    $data = [];
	    foreach ($channels as $station) {
	        $data[] = $station->channel_name;
	    }
        
        echo json_encode($data);
	    exit();
	}
}