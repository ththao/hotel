<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rent_additional_fee_model extends MY_Model {

    public function __construct()
    {
        $this->set_table_name('rent_additional_fee');

        // Call the CI_Model constructor
        parent::__construct();
    }
    
    public function getFee($rent_id)
    {
        $query = $this->db->select('SUM(amount) AS price')->from('rent_additional_fee')->where('rent_id', $rent_id)->get();
        $data = $query->row();
        return $data ? $data->price : 0;
    }
    
    public function getFeeList($rent_id)
    {
        $query = $this->db->select('*')->from('rent_additional_fee')->where('rent_id', $rent_id)->order_by('id')->get();
        return $query->result();
    }
}