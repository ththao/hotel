<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rent_receive_model extends MY_Model {

    public function __construct()
    {
        $this->set_table_name('rent_receive');

        // Call the CI_Model constructor
        parent::__construct();
    }
    
    public function countReceived($rent_id)
    {
        $received = array(
            'id_card' => 0,
            'passport' => 0,
            'driving_license' => 0,
            'bike' => 0
        );
        
        $query = $this->db->query('SELECT type, COUNT(*) AS cnt FROM rent_receive WHERE rent_id = ' . $rent_id . ' AND (early_return = 0 OR early_return IS NULL) GROUP BY type');
        $data = $query->result();
        if (!empty($data)) {
            foreach ($data as $item) {
                $received[$item->type] = $item->cnt;
            }
        }
        
        return $received;
    }
}