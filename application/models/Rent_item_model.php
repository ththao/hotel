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
        $query = $this->db->query('SELECT SUM(quantity*unit_price) AS price FROM rent_item WHERE rent_id = ' . $rent_id);
        $data = $query->result();
        if (!empty($data)) {
            $data = $data[0];
            return $data->price;
        }
        
        return 0;
    }
}