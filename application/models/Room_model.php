<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Room_model extends MY_Model {

    public function __construct()
    {
        $this->set_table_name('room');

        // Call the CI_Model constructor
        parent::__construct();
    }
    
    public function getRooms()
    {
        $sql = '
            SELECT room.*, rent.id AS rent_id, rent.check_in, rent.check_out, rent.hourly,
                rent.human, rent.used_items_price, rent.total_price, rent.note, rent.prepaid, rent.notes
            FROM room
            LEFT OUTER JOIN rent ON room_id = room.id AND check_in IS NOT NULL AND (check_out IS NULL OR check_out = "")
            WHERE room.user_id = ' . $this->session->userdata('user_id') . ' AND removed = 0
            ORDER BY room.floor, room.name
        ';
        $query = $this->db->query($sql);
        $data = $query->result();
        
        return $data;
    }
    
    public function getRoom($rent_id)
    {
        $sql = '
            SELECT room.*, rent.id AS rent_id, rent.check_in, rent.check_out, rent.hourly,
                rent.human, rent.used_items_price, rent.total_price, rent.note, rent.prepaid, rent.notes
            FROM room
            LEFT OUTER JOIN rent ON room_id = room.id
            WHERE room.user_id = ' . $this->session->userdata('user_id') . ' AND removed = 0 AND rent.id = ' . $rent_id . '
            ORDER BY floor
        ';
        $query = $this->db->query($sql);
        $data = $query->result();
        
        if (!empty($data)) {
            return $data[0];
        }
        
        return array();
    }
}