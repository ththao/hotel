<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Item extends My_Controller {
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
        
        if (!$this->session->has_userdata('logged_in') || !$this->session->userdata('logged_in')) {
	    	if ($this->input->is_ajax_request()) {
	    		echo json_encode(array('status' => 0));
	    	} else {
            	redirect('/auth/admin');
	    	}
        }

        $this->loadModel(array('item_model'));
    }
    
    public function index()
    {
        $items = $this->item_model->findAll(array('user_id' => $this->session->userdata('user_id')));
        $this->render('item/index', array('items' => $items));
    }
    
    public function create()
    {
        if ($data = $this->input->post()) {
            $data['big_icon_class'] = 'big-' . $data['icon_class'];
            $data['user_id'] = $this->session->userdata('user_id');
            
            $id = $this->item_model->save($data);
            if ($id) {
            	redirect("/item/update/" . $id);
            }
        }
        
        $this->render('item/create');
    }
    
    public function update($id)
    {
        if (!$id) {
            redirect("/item/create");
        }
        $item = $this->item_model->findOne(array('id' => $id, 'user_id' => $this->session->userdata('user_id')));
        if (!$item) {
        	redirect("/item");
        }
        
        if ($data = $this->input->post()) {
            $this->item_model->update($item->id, $data);
            
            $item = $this->item_model->findOne(array('id' => $id, 'user_id' => $this->session->userdata('user_id')));
        }
        
        $this->render('item/update', array('item' => $item));
    }
    
    public function delete($id)
    {
        $item = $this->item_model->findOne(array('id' => $id, 'user_id' => $this->session->userdata('user_id')));
        if ($item) {
        	$this->item_model->update($item->id, array('removed' => 1));
        }
        
        redirect("/item");
    }
    
    public function getback($id)
    {
        $item = $this->item_model->findOne(array('id' => $id, 'user_id' => $this->session->userdata('user_id')));
        if ($item) {
        	$this->item_model->update($item->id, array('removed' => 0));
        }
        
        redirect("/item");
    }
}