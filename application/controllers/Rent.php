<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rent extends My_Controller
{   
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

        $this->loadModel(array('room_model', 'rent_model', 'item_model', 'rent_item_model', 'rent_receive_model'));
    }

	public function index()
	{
	    if (!$this->session->has_userdata('logged_in') || !$this->session->userdata('logged_in')) {
	        redirect('/auth/admin');
	    }
	    
	    $this->load->model('user_model');
	    $user = $this->user_model->findOne(array('id' => $this->session->userdata('user_id')));
	    if (!$user->report_enable) {
	        redirect('/auth/admin');
	    }
	    
	    if ($this->input->get()) {
    	    $date = $this->input->get('date');
    	    $month = $this->input->get('month');
    	    $year = $this->input->get('year');
	    } else {
	        $date = date("d");
	        $month = date("m");
	        $year = date("Y");
	    }
	    $data = $this->rent_model->getList($year, $month, $date);
	    
		$this->render('rent/index', array('data' => $data, 'date' => $date, 'month' => $month, 'year' => $year));
	}
	
	public function view($id)
	{
	    if (!$id) {
	        redirect("/site");
	    }
	    $room = $this->room_model->getRoom($id);
	    if (empty($room)) {
	        redirect("/site");
	    }
	    
	    if (isset($room->rent_id) && $room->rent_id) {
	        $room->items_used = $this->rent_model->getItemsUsed($room->rent_id);
	        $room->items_received = $this->rent_model->getItemsReceived($room->rent_id);
	    }

	    if ($room->check_out) {
	        $this->render('rent/detail', array('room' => $room));
	    } else {
	        $rent = $this->rent_model->findOne(array('id' => $room->rent_id, 'user_id' => $this->session->userdata('user_id')));
	        if ($rent) {
    	        $tempTotal = $this->rent_model->calculatePrice($room, $rent, array(
    	            'check_in' => $rent->check_in,
    	            'check_out' => time(),
    	            'used_items_price' => $this->rent_item_model->getPrice($rent->id),
    	            'hourly' => $rent->hourly
    	        ));
    	        $tempTotal['notes'] = $rent->notes;
    	        $items = $this->item_model->findAll(array('removed' => 0, 'user_id' => $this->session->userdata('user_id')));
    	        
    	        $free_rooms = $this->room_model->getRooms();
    	        $this->render('rent/view', array('room' => $room, 'items' => $items, 'data' => $tempTotal, 'free_rooms' => $free_rooms));
	        } else {
	            redirect("/site");
	        }
	    }
	}
	
	public function changeType()
	{
	    $id = $this->input->post('rent_id');
	    if (!$id) {
	        echo json_encode(array('status' => 0));
	        exit;
	    }
	    
	    $rent = $this->rent_model->findOne(array('id' => $id, 'user_id' => $this->session->userdata('user_id')));
	    if (empty($rent)) {
	        echo json_encode(array('status' => 0));
	        exit;
	    }
	    
	    $this->rent_model->update($id, array('hourly' => $rent->hourly ? 0 : 1));
        echo json_encode(array('status' => 1, 'hourly' => $rent->hourly ? 0 : 1));
        exit;
	}
	
	public function change_room()
	{
	    $id = $this->input->post('rent_id');
	    if (!$id) {
	        echo json_encode(array('status' => 0, 'message' => 'Thông tin thuê phòng không tồn tại hoặc đã hủy.'));
	        exit;
	    }
	    
	    $rent = $this->rent_model->findOne(array('id' => $id, 'user_id' => $this->session->userdata('user_id')));
	    if (empty($rent)) {
	        echo json_encode(array('status' => 0, 'message' => 'Thông tin thuê phòng không tồn tại hoặc đã hủy.'));
	        exit;
	    }
	    
	    $this->rent_model->update($id, array('room_id' => $this->input->post('room_id')));
	    echo json_encode(array('status' => 1));
	    exit;
	}
	
	public function update($id)
	{
		if (!$id) {
			redirect("/site");
		}
		if (!$this->session->has_userdata('logged_in') || !$this->session->userdata('logged_in')) {
			redirect('/auth/admin');
		}
		
		$room = $this->room_model->getRoom($id);
		if (empty($room)) {
			redirect("/site");
		}
		
		if ($_POST) {
			$this->load->model('rent_model');
			
			$data = array(
				'check_in' => strtotime($_POST['check_in']),
			    'check_out' => $_POST['check_out'] ? strtotime($_POST['check_out']) : null,
				'total_price' => $_POST['total_price'],
				'note' => $_POST['note'],
				'human' => $_POST['human']
			);
			
			$rent = $this->rent_model->findOne(array('id' => $id, 'user_id' => $this->session->userdata('user_id')));
			if ($rent) {
			    if (!$rent->bk_note) {
			        $data['bk_note'] = $rent->note;
			        $data['bk_total_price'] = $rent->total_price;
			        $data['bk_created_at'] = time();
			    }
			    if (!$rent->bk_check_in) {
			        $data['bk_check_in'] = $rent->check_in;
			    }
			    if (!$rent->bk_check_out) {
			        $data['bk_check_out'] = $rent->check_out;
			    }
			}
			
			$this->rent_model->update($room->rent_id, $data);
			
			redirect("/rent/view/" . $id);
		}
		 
		if (isset($room->rent_id) && $room->rent_id) {
			$room->items_used = $this->rent_model->getItemsUsed($room->rent_id);
			$room->items_received = $this->rent_model->getItemsReceived($room->rent_id);
		}
	
		$this->render('rent/update', array('room' => $room));
	}
	
	public function remove($id)
	{
		if (!$id) {
			redirect("/site");
		}
		if (!$this->session->has_userdata('logged_in') || !$this->session->userdata('logged_in')) {
			redirect('/auth/admin');
		}
		
		$room = $this->room_model->getRoom($id);
		if (empty($room)) {
			redirect("/site");
		}
		
		$this->db->insert('rent_cancel', [
		    'user_id' => $this->session->userdata('user_id'),
		    'room_id' => $room->id,
		    'check_in' => $room->check_in,
		    'check_out' => $room->check_out,
		    'cancel_at' => time()
	    ]);
		
		$this->db->where('rent_id', $id);
		$this->db->delete('rent_receive');
		
		$this->db->where('rent_id', $id);
		$this->db->delete('rent_item');
		
		$this->db->where('id', $id);
		$this->db->delete('rent');
		
		redirect("/rent");
	}
	
	public function check_status()
	{
		$rent_id = $this->input->post('rent_id');
		$room = $this->room_model->getRoom($rent_id);
		if (!$room) {
			json_encode(array('status' => 1, 'using' => 0));
		}
		
		echo json_encode(array('status' => 1, 'using' => $room->check_out ? 0 : 1));
	}
	
	public function add_item()
	{
	    $rent_id = $this->input->post('rent_id');
	    $item_id = $this->input->post('item_id');
	    
	    $rent = $this->rent_model->findOne(array('id' => $rent_id, 'user_id' => $this->session->userdata('user_id')));
	    $item = $this->item_model->findOne(array('id' => $item_id, 'user_id' => $this->session->userdata('user_id')));
	    
	    if (!$rent || !$item) {
	        echo json_encode(array('status' => 0));
	        exit();
	    }
	    
	    $data = array(
	        'rent_id' => $rent_id,
	        'item_id' => $item_id
	    );
	    $rentItem = $this->rent_item_model->findOne($data);
	    if ($rentItem) {
	        $this->rent_item_model->update($rentItem->id, array('quantity' => $rentItem->quantity + 1));
	    } else {
	        $data['quantity'] = 1;
	        $data['unit_price'] = $item->price;
	        $this->rent_item_model->save($data);
	    }
	    
	    $room = $this->room_model->getRoom($rent->id);
	    $tempTotal = $this->rent_model->calculatePrice($room, $rent, array(
	        'check_in' => $rent->check_in,
	        'check_out' => time(),
	        'used_items_price' => $this->rent_item_model->getPrice($rent->id),
	        'hourly' => $rent->hourly
	    ));
	    $html = $this->load->view('rent/total', array('room' => $room, 'data' => $tempTotal), true);
	    
	    echo json_encode(array(
	        'status' => 1, 'rent_id' => $rent_id, 
	        'item_id' => $item_id, 'icon_class' => $item->icon_class, 
	        'name' => $item->name,
	        'total_html' => $html
	    ));
	    exit();
	}
	
	public function add_prepaid()
	{
	    $rent_id = $this->input->post('rent_id');
	    $rent = $this->rent_model->findOne(array('id' => $rent_id, 'user_id' => $this->session->userdata('user_id')));
	     
	    if (!$rent) {
	        echo json_encode(array('status' => 0));
	        exit();
	    }
	     
	    $this->rent_model->update($rent->id, array('prepaid' => $rent->prepaid + floatval($this->input->post('amount'))));
	    
	    $room = $this->room_model->getRoom($rent->id);
	    $tempTotal = $this->rent_model->calculatePrice($room, $rent, array(
	        'check_in' => $rent->check_in,
	        'check_out' => time(),
	        'used_items_price' => $this->rent_item_model->getPrice($rent->id),
	        'hourly' => $rent->hourly
	    ));
	    $tempTotal['notes'] = $rent->notes;
	    $html = $this->load->view('rent/total', array('room' => $room, 'data' => $tempTotal), true);
	     
	    echo json_encode(array('status' => 1, 'total_html' => $html));
	    exit();
	}
	
	public function update_notes()
	{
	    $rent_id = $this->input->post('rent_id');
	    $rent = $this->rent_model->findOne(array('id' => $rent_id, 'user_id' => $this->session->userdata('user_id')));
	    
	    if (!$rent) {
	        echo json_encode(array('status' => 0));
	        exit();
	    }
	    
	    $this->rent_model->update($rent->id, array('notes' => $this->input->post('notes')));
	    
	    $room = $this->room_model->getRoom($rent->id);
	    $tempTotal = $this->rent_model->calculatePrice($room, $rent, array(
	        'check_in' => $rent->check_in,
	        'check_out' => time(),
	        'used_items_price' => $this->rent_item_model->getPrice($rent->id),
	        'hourly' => $rent->hourly
	    ));
	    $tempTotal['notes'] = $this->input->post('notes');
	    $html = $this->load->view('rent/total', array('room' => $room, 'data' => $tempTotal), true);
	    
	    echo json_encode(array('status' => 1, 'total_html' => $html));
	    exit();
	}
	
	public function get_received_items()
	{
		$rent_id = $this->input->post('rent_id');
		$type = $this->input->post('type');
		
		$this->db->select('rent_receive.id, rent_receive.type, card.name, COALESCE(card.number, rent_receive.number) AS number');
		$this->db->from('rent_receive');
		$this->db->join('card', 'card.id = rent_receive.card_id', 'LEFT OUTER');
		$this->db->where(array('rent_receive.rent_id' => $rent_id, 'rent_receive.type' => $type, 'rent_receive.early_return' => 0));
		$query = $this->db->get();
		
		if ($items = $query->result()) {
		    $html = $this->load->view('rent/received_item', array('items' => $items), true);
			
			echo json_encode(array('status' => 1, 'html' => $html));
			exit();
		}
		
		echo json_encode(array('status' => 0));
		exit();
	}
	
	public function receive_item_detail()
	{
	    $rent_receive = $this->rent_receive_model->findOne(array('id' => $this->input->post('item_receive_id')));
	    if ($rent_receive) {
	        if ($rent_receive->card_id) {
	            $this->load->model('card_model');
	            $card = $this->card_model->findOne(array('id' => $rent_receive->card_id));
	            $rent_receive->name = $card->name;
	            $rent_receive->number = $card->number;
	            $rent_receive->nation = $card->nation;
	            $rent_receive->birthday = $card->birthday;
	            $rent_receive->address = $card->address;
	            $rent_receive->gender = $card->gender;
	        }
	        
	        echo json_encode(array('status' => 1, 'item' => $rent_receive));
	        return;
	    }
	    
	    echo json_encode(array('status' => 0));
	}
	
	public function return_receive_item()
	{
		$rent_receive = $this->rent_receive_model->findOne(array('id' => $this->input->post('item_receive_id')));
		
		if ($rent_receive) {
			$this->rent_receive_model->update($rent_receive->id, array('early_return' => time()));
		}
		
		echo json_encode(array('status' => 1));
	}
	
	public function remove_receive_item()
	{
	    $rent_receive = $this->rent_receive_model->findOne(array('id' => $this->input->post('item_receive_id')));
	    
	    if ($rent_receive) {
	        $this->rent_receive_model->delete($rent_receive->id);
	    }
	    
	    echo json_encode(array('status' => 1));
	}
	
	public function remove_item()
	{
	    $data = array(
	        'rent_id' => $this->input->post('rent_id'),
	        'item_id' => $this->input->post('item_id')
	    );
	    $rentItem = $this->rent_item_model->findOne($data);
	    if ($rentItem) {
	        if ($rentItem->quantity == 1) {
	            $this->rent_item_model->delete($rentItem->id);
	        } else {
	           $this->rent_item_model->update($rentItem->id, array('quantity' => $rentItem->quantity - 1));
	        }
	    }
	    
	    $rent = $this->rent_model->findOne(array('id' => $this->input->post('rent_id'), 'user_id' => $this->session->userdata('user_id')));
	    $room = $this->room_model->getRoom($rent->id);
	    $tempTotal = $this->rent_model->calculatePrice($room, $rent, array(
	        'check_in' => $rent->check_in,
	        'check_out' => time(),
	        'used_items_price' => $this->rent_item_model->getPrice($rent->id),
	        'hourly' => $rent->hourly
	    ));
	    $html = $this->load->view('rent/total', array('room' => $room, 'data' => $tempTotal), true);
	    
	    echo json_encode(array('status' => 1, 'total_html' => $html));
	    exit();
	}
	
	public function receive_item()
	{
        $rent = $this->rent_model->findOne(array('id' => $this->input->post('rent_id'), 'user_id' => $this->session->userdata('user_id')));
        if (!$rent) {
            echo json_encode(array('status' => 0));
            exit();
        }
        
        $card_id = null;
        if ('bike' != $this->input->post('type')) {
            $this->load->model('card_model');
            $card = $this->card_model->findOne(array('number' => strtoupper($this->input->post('number'))));
            
            if ($card) {
                $fields = array();
                if ($this->input->post('name') && $this->formatCardName($this->input->post('name')) != $card->name) {
                    $fields['name'] = $this->formatCardName($this->input->post('name'));
                }
                if ($this->input->post('number') && strtoupper($this->input->post('number')) != $card->number) {
                    $fields['number'] = strtoupper($this->input->post('number'));
                }
                if ($this->input->post('nation') && $this->formatCardName($this->input->post('nation')) != $card->nation) {
                    $fields['nation'] = $this->formatCardName($this->input->post('nation'));
                }
                if ($this->input->post('birthday') && $this->input->post('birthday') != $card->birthday) {
                    $fields['birthday'] = $this->input->post('birthday');
                }
                if ($this->input->post('address') && $this->formatCardName($this->input->post('address')) != $card->address) {
                    $fields['address'] = $this->formatCardName($this->input->post('address'));
                }
                if ($this->input->post('gender') && $this->input->post('gender') != $card->gender) {
                    $fields['gender'] = $this->input->post('gender');
                }
                if ($fields) {
                    $this->card_model->update($card->id, $fields);
                }
                $card_id = $card->id;
            } else {
                $card_id = $this->card_model->save(array(
                    'type' => $this->input->post('type'),
                    'name' => $this->formatCardName($this->input->post('name')),
                    'number' => strtoupper($this->input->post('number')),
                    'nation' => $this->formatCardName($this->input->post('nation')),
                    'birthday' => $this->input->post('birthday'),
                    'address' => $this->formatCardName($this->input->post('address')),
                    'gender' => $this->input->post('gender')
                ));
            }
        }
        
	    $rent_receive = $this->rent_receive_model->findOne(array('id' => $this->input->post('item_receive_id')));
	    if ($rent_receive) {
	        if ($rent_receive->type == 'bike') {
                $this->rent_receive_model->update($rent_receive->id, array('number' => strtoupper($this->input->post('number'))));
	        } else {
	            $this->rent_receive_model->update($rent_receive->id, array('card_id' => $card_id, 'number' => strtoupper($this->input->post('number'))));
	        }
	    } else {
    	    $this->rent_receive_model->save(array(
    	        'rent_id' => $rent->id,
    	        'type' => $this->input->post('type'),
    	        'card_id' => $card_id,
    	        'number' => strtoupper($this->input->post('number'))
    	    ));
	    }
	    
	    $room = $this->room_model->getRoom($rent->id);
	    if ($room && $room->rent_id) {
	        $room->items_received = $this->rent_model->getItemsReceived($room->rent_id);
	    }
	    $html = $this->load->view('rent/items', array('room' => $room), true);
	    
	    echo json_encode(array('status' => 1, 'html' => $html));
	    exit();
	}
	
	private function formatCardName($name)
	{
	    $mapping = array(
	        'đ' => 'Đ',
	        'á' => 'Á',
	        'à' => 'À',
	        'ả' => 'Ả',
	        'ã' => 'Ã',
	        'ạ' => 'Ạ',
	        'â' => 'Â',
	        'ấ' => 'Ấ',
	        'ẩ' => 'Ẩ',
	        'ẫ' => 'Ẫ',
	        'ậ' => 'Ậ',
	        'ă' => 'Ă',
	        'ắ' => 'Ắ',
	        'ằ' => 'Ằ',
	        'ẳ' => 'Ẳ',
	        'ẵ' => 'Ẵ',
	        'ặ' => 'Ặ',
	        'ê' => 'Ê',
	        'ế' => 'Ế',
	        'ề' => 'Ề',
	        'ể' => 'Ể',
	        'ễ' => 'Ễ',
	        'ệ' => 'Ệ',
	        'ô' => 'Ô',
	        'ố' => 'Ố',
	        'ồ' => 'Ồ',
	        'ổ' => 'Ổ',
	        'ỗ' => 'Ỗ',
	        'ộ' => 'Ộ',
	        'ơ' => 'Ơ',
	        'ớ' => 'Ớ',
	        'ờ' => 'Ờ',
	        'ở' => 'Ở',
	        'ỡ' => 'Ỡ',
	        'ợ' => 'Ợ',
	        'ú' => 'Ú',
	        'ù' => 'Ù',
	        'ủ' => 'Ủ',
	        'ũ' => 'Ũ',
	        'ụ' => 'Ụ',
	        'ư' => 'Ư',
	        'ứ' => 'Ứ',
	        'ừ' => 'Ừ',
	        'ử' => 'Ử',
	        'ữ' => 'Ữ',
	        'ự' => 'Ự'
        );
	    $name = ucwords($name);
	    foreach ($mapping as $search => $replace) {
	        $name = str_replace(' ' . $search, ' ' . $replace, $name);
	        if (strpos($name, $search) == 0) {
	            $name = str_replace($search, $replace, $name);
	        }
	    }
	    
	    return $name;
	}
	
	public function add_remove_human()
	{
	    $rent = $this->rent_model->findOne(array('id' => $this->input->post('rent_id'), 'user_id' => $this->session->userdata('user_id')));
	    if (!$rent) {
	        echo json_encode(array('status' => 0));
	        exit();
	    }
	    
	    $human = $rent->human + $this->input->post('add');
	    $this->rent_model->update($rent->id, array('human' => $human));
	    $rent->human = $human;
	    
	    $room = $this->room_model->getRoom($rent->id);
	    if ($room && $room->rent_id) {
	        $room->items_received = $this->rent_model->getItemsReceived($room->rent_id);
	    }
	    $html = $this->load->view('rent/items', array('room' => $room), true);
	    
	    $tempTotal = $this->rent_model->calculatePrice($room, $rent, array(
	        'check_in' => $rent->check_in,
	        'check_out' => time(),
	        'used_items_price' => $this->rent_item_model->getPrice($rent->id),
	        'hourly' => $rent->hourly
	    ));
	    $total_html = $this->load->view('rent/total', array('room' => $room, 'data' => $tempTotal), true);
	    
	    echo json_encode(array('status' => 1, 'html' => $html, 'total_html' => $total_html));
	    exit();
	}
	
	public function checkout()
	{
	    $rent = $this->rent_model->findOne(array('id' => $this->input->post('rent_id'), 'user_id' => $this->session->userdata('user_id')));
	    if (!$rent) {
	        echo json_encode(array('status' => 0));
	        exit();
	    }
	    if ($this->rent_model->checkout($rent)) {
    	    echo json_encode(array('status' => 1));
    	    exit();
	    }
	    
	    echo json_encode(array('status' => 0));
	    exit();
	}
	
	public function cancel()
	{
	    $rent = $this->rent_model->findOne(array('id' => $this->input->post('rent_id'), 'user_id' => $this->session->userdata('user_id')));
	    if (!$rent) {
	        echo json_encode(array('status' => 1));
	        exit();
	    }
	    
	    $this->db->insert('rent_cancel', [
		    'user_id' => $this->session->userdata('user_id'),
		    'room_id' => $rent->room_id,
		    'check_in' => $rent->check_in,
		    'check_out' => $rent->check_out,
		    'cancel_at' => time()
	    ]);
	    
	    $sql = 'DELETE FROM rent_item WHERE rent_id = ' . $rent->id;
        $this->db->query($sql);

        $sql = 'DELETE FROM rent_receive WHERE rent_id = ' . $rent->id;
        $this->db->query($sql);
        
        $sql = 'DELETE FROM rent WHERE id = ' . $rent->id;
        $this->db->query($sql);
        
        echo json_encode(array('status' => 1));
        exit();
	}
	
	public function complete_card()
	{
	    $this->load->model('card_model');
	    $item = $this->card_model->findOne(array('number' => strtoupper($this->input->post('number'))));
	    
	    if ($item) {
	        echo json_encode(array(
	            'status' => 1,
	            'type' => $item->type,
	            'card_id' => $item->id,
	            'number' => $item->number,
	            'name' => $item->name,
	            'address' => $item->address,
	            'birthday' => $item->birthday,
	            'gender' => $item->gender,
	            'nation' => $item->nation
	        ));
	        exit();
	    }
	    
	    echo json_encode(array('status' => 0));
	    exit();
	}
}