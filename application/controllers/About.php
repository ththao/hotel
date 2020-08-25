<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class About extends My_Controller {
    
    public function __construct()
    {
        parent::__construct();
    }

	public function index()
	{
        $this->render('about/index');
	}
	
	public function pay_request()
	{
	    if ($this->session->has_userdata('user_id')) {
	        $id = $this->session->userdata('user_id');
	    } else {
	        $id = $this->input->post('user_id');
	    }
	    if ($id) {
	        $this->load->model('pay_request_model');
	        
	        $pay_request = $this->pay_request_model->findOne(array(
	            'user_id' => $id,
	            'executed_at IS NULL'
	        ));
	        
	        if ($pay_request) {
	            $this->pay_request_model->update($pay_request->id, array(
	                'requested_at' => time()
	            ));
	        } else {
	            $this->pay_request_model->save(array(
	                'user_id' => $id,
	                'requested_at' => time()
	            ));
	        }
	        
	        echo json_encode(array('status' => 1, 'message' => 'Yêu cầu gia hạn đã được gửi, chúng tôi sẽ liên hệ với bạn sớm, xin cám ơn.'));
	        exit();
	    }
	    
	    echo json_encode(array('status' => 0));
	    exit();
	}
}