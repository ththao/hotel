<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rent_item_model extends MY_Model {

    public function __construct()
    {
        $this->set_table_name('rent_item');

        // Call the CI_Model constructor
        parent::__construct();
    }
    
    public function getPrice($rent_id)
    {
        $query = $this->db->select('SUM(quantity*unit_price) AS price')->from('rent_item')->where('rent_id', $rent_id)->get();
        $data = $query->row();
        return $data ? $data->price : 0;
    }
}