<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Site extends My_Controller {
    public function __construct()
    {
        parent::__construct();

        $this->loadModel(array('room_model', 'rent_model'));
        
	    if (!$this->checkLoggedIn()) {
            redirect('/auth/login');
        }
        if ($this->session->userdata('user_id') != 1) {
            $this->session->unset_userdata('logged_in');
        }
    }

	public function index()
	{
	    $floors = array();
	    $rooms = $this->room_model->getRooms();
	    
	    $status = '';
	    foreach ($rooms as $room) {
	        if ($room->rent_id) {
	        	$status .= '1';
	        	$room->items_used = $this->rent_model->getItemsUsed($room->rent_id);
	        	$room->items_received = $this->rent_model->getItemsReceived($room->rent_id);
	        } else {
	        	$status .= '0';
	        }
	        $floors[$room->floor][] = $room;
	    }
	    
	    $this->load->model('user_model');
	    $user = $this->user_model->findOne(array('id' => $this->session->userdata('user_id')));
	    
		$this->render('site/index', array('floors' => $floors, 'room_statuses' => $status, 'expired' => $user->expired_at));
	}
	
	public function check_status()
	{
		$rooms = $this->room_model->getRooms();
		$status = '';
		foreach ($rooms as $room) {
			if ($room->rent_id) {
				$status .= '1';
			} else {
				$status .= '0';
			}
		}
		
		echo json_encode(array('status' => 1, 'room_statuses' => $status));
	}

	public function checkin()
	{
	    $rent = $this->rent_model->findWorkingRoom($this->input->get('room_id'));
	    
	    if (!$rent) {
            $rent_id = $this->rent_model->checkin($this->input->get('room_id'), $this->input->get('hourly'));
	    } else {
	        $rent_id = $rent->id;
	    }
	    
		redirect("/rent/view/" . $rent_id);
	}
}