<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Paid extends My_Controller {
    public function __construct()
    {
        parent::__construct();
        
	    if (!$this->checkLoggedIn()) {
            redirect('/auth/login');
        }
        
        if (!$this->session->has_userdata('logged_in') || !$this->session->userdata('logged_in')) {
            redirect('/auth/admin');
        }

        $this->loadModel(array('paid_model'));
    }
    
    public function index()
    {
        $month = $this->input->get('month') ? $this->input->get('month') : date('m');
        $year = $this->input->get('year') ? $this->input->get('year') : date('Y');
        
        $this->db->from('paid');
        $this->db->where('user_id', $this->session->userdata('user_id'));
        $this->db->where('paid_date LIKE "%' . $month . '-' . $year . '"');
        $this->db->order_by('paid_date, reason');
        $query = $this->db->get();
        
        $data = $query->result();
        $this->render('paid/index', array('data' => $data, 'month' => $month, 'year' => $year));
    }
    
    public function create()
    {
        if ($data = $this->input->post()) {
        	$data['user_id'] = $this->session->userdata('user_id');
        	$this->paid_model->save($data);
            
            redirect("/paid");
        }
        
        $this->render('paid/create');
    }
    
    public function update($id)
    {
        if (!$id) {
            redirect("/paid/create");
        }
        $item = $this->paid_model->findOne(array('id' => $id, 'user_id' => $this->session->userdata('user_id')));
        if (!$item) {
        	redirect("/paid");
        }
        
        if ($data = $this->input->post()) {
            $this->paid_model->update($item->id, $data);
        }
        
        $item = $this->paid_model->findOne(array('id' => $id, 'user_id' => $this->session->userdata('user_id')));
        $this->render('paid/update', array('item' => $item));
    }
    
    public function delete($id)
    {
        $this->paid_model->delete($id);
        
        redirect("/paid");
    }
}