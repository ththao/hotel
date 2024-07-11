<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Room extends My_Controller {
    public function __construct()
    {
        parent::__construct();
        
	    if (!$this->checkLoggedIn()) {
            redirect('/auth/login');
        }
        
        if (!$this->session->has_userdata('logged_in') || !$this->session->userdata('logged_in')) {
            redirect('/auth/admin');
        }

        $this->loadModel(array('room_model'));
    }
    
    public function index()
    {
        $rooms = $this->room_model->findAll(array('user_id' => $this->session->userdata('user_id')));
        $this->render('room/index', array('rooms' => $rooms));
    }
    
    public function create()
    {
        if ($data = $this->input->post()) {
        	$data['user_id'] = $this->session->userdata('user_id');
            $this->room_model->save($data);
            
            redirect("/room");
        }
        
        $this->render('room/create');
    }
    
    public function prices()
    {   
        $rooms = $this->room_model->findAll(array('user_id' => $this->session->userdata('user_id')));
        $res = ['rooms' => $rooms];
        if ($data = $this->input->post()) {
            if (isset($data['rooms']) && isset($data['price_name']) && $data['price_name'] && isset($data['new_price']) && $data['new_price']) {
                $this->db->where('user_id', $this->session->userdata('user_id'));
                $this->db->where_in('id', $data['rooms']);
                $this->db->update('room', [$data['price_name'] => $data['new_price']]);
                
                $res['message'] = 'Đã cập nhật giá thành công';
            }
        }
        
        $this->render('room/prices', $res);
    }
    
    public function update($id)
    {
        if (!$id) {
            redirect("/room/create");
        }
        $room = $this->room_model->findOne(array('id' => $id, 'user_id' => $this->session->userdata('user_id')));
        if (!$room) {
        	redirect("/room");
        }
        
        if ($data = $this->input->post()) {
            /*if (isset($data['rooms'])) {
                $this->db->where('user_id', $this->session->userdata('user_id'));
                $this->db->where_in('id', $data['rooms']);
                $this->db->update('room', [
                    'hourly_price' => $data['hourly_price'],
                    'next_hourly_price' => $data['next_hourly_price'],
                    'night_price' => $data['night_price'],
                    'daily_price' => $data['daily_price']
                ]);
                unset($data['rooms']);
            }*/
            $this->room_model->update($room->id, $data);
        }
        
        $room = $this->room_model->findOne(array('id' => $id, 'user_id' => $this->session->userdata('user_id')));
        $rooms = $this->room_model->findAll(array('user_id' => $this->session->userdata('user_id')));
        $this->render('room/update', array('room' => $room, 'rooms' => $rooms));
    }
    
    public function delete($id)
    {
    	$room = $this->room_model->findOne(array('id' => $id, 'user_id' => $this->session->userdata('user_id')));
    	
    	if ($room) {
        	$this->room_model->update($room->id, array('removed' => 1));
    	}
        
        redirect("/room");
    }
    
    public function getback($id)
    {
    	$room = $this->room_model->findOne(array('id' => $id, 'user_id' => $this->session->userdata('user_id')));
    	
    	if ($room) {
        	$this->room_model->update($room->id, array('removed' => 0));
    	}
        
        redirect("/room");
    }
}