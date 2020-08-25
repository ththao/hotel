<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class My_Controller extends CI_Controller
{
    public $layout = 'layout/main';
    
    public function __construct()
    {
        parent::__construct();
        
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        
        if ($this->session->has_userdata('user_id') && $this->session->userdata('user_id')) {
            if ($this->session->userdata('user_id') == 1) {
                $this->session->set_userdata('logged_in', 1);
            }
            
            $this->load->model('user_model');
            $user = $this->user_model->findOne(array('id' => $this->session->userdata('user_id')));
            $this->session->set_userdata('expired_at', $user->expired_at);
        }
    }

    public function layout($layout)
    {
        $this->layout = $layout;
    }

    public function loadModel($listModel = array())
    {
        foreach($listModel as $model) {
            $this->load->model($model);
        }
    }

    public function render($link, $data = null)
    {
        $this->load->view($this->layout, array(
            'content' => array(
                'link' => $link,
                'data' => $data,
            )
        ));
    }
    
    public function checkLoggedIn()
    {
        if (!$this->session->has_userdata('user_id') || !$this->session->userdata('user_id')) {
            $this->session->unset_userdata('user_id');
            $this->session->unset_userdata('fullname');
            $this->session->unset_userdata('logged_in');
            return false;
        }
        
        $this->load->model('user_model');
        $user = $this->user_model->findOne(array(
            'id' => $this->session->userdata('user_id'),
            '(deleted_at IS NULL OR deleted_at = "")' => null
        ));
        if (!$user) {
            $this->session->unset_userdata('user_id');
            $this->session->unset_userdata('fullname');
            $this->session->unset_userdata('logged_in');
            delete_cookie('siteAuth');
            return false;
        }
        return true;
    }
    
    protected function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}