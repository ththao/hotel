<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Site extends My_Controller {
    public function __construct()
    {
        parent::__construct();

        $this->loadModel(array('room_model', 'rent_model', 'rent_item_model'));
        
	    if (!$this->checkLoggedIn() && $this->router->method != 'backup') {
            redirect('/auth/login');
        }
        if ($this->session->userdata('user_id') != 1 && $this->router->fetch_method() != 'check_status') {
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
	
	public function backup()
	{
	    try {
    	    $this->db->select('rent.*, users.name AS hotel_name');
    	    $this->db->from('rent');
    	    $this->db->join('users', 'users.id = rent.user_id');
    	    $this->db->where('check_out IS NULL', null);
    	    $this->db->order_by('users.id, rent.room_id');
    	    $query = $this->db->get();
    	    
    	    $rents = $query->result();
    	    
    	    $html = '';
            if ($rents) {
                foreach ($rents as $rent) {
        	        $room = $this->room_model->getRoomByRentId($rent->id);
        	        if ($room) {
                	    $tempTotal = $this->rent_model->calculatePrice($room, $rent, array(
                	        'check_in' => $rent->check_in,
                	        'check_out' => time(),
                	        'used_items_price' => $this->rent_item_model->getPrice($rent->id),
                	        'hourly' => $rent->hourly
                	    ));
                	    $html .= '<div style="border: 1px #000 solid;">';
                	    $html .= '<div>Khách sạn ' . $rent->hotel_name . ' - Phòng ' . $room->name . '</div>';
                	    $html .= $this->load->view('rent/total', array('room' => $room, 'data' => $tempTotal), true);
                	    $html .= '</div>';
        	        }
                }
            }
            echo $html;
            
            file_put_contents('backup.txt', $html);
            
            /*$headers = array("From: ththao@ceresolutions.com",
                "Reply-To: ththao@ceresolutions.com",
                "X-Mailer: PHP/" . PHP_VERSION
            );
            $headers = implode("\r\n", $headers);
            $res = mail("ththao@ceresolutions.com", "Backup phòng", $html, $headers);
            var_dump($res);*/
            
            /*$this->load->library('email');
            $this->email->from('ththao@ceresolutions.com', 'Me');
            $this->email->to('ththao@ceresolutions.com');
            $this->email->subject('Backup phòng');
            $this->email->message($html);
            $this->email->send();*/
	    } catch (Exception $e) {
	        print_r($e);
	    }
	}
}