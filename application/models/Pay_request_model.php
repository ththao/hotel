<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pay_request_model extends MY_Model {

    public function __construct()
    {
        $this->set_table_name('pay_request');

        // Call the CI_Model constructor
        parent::__construct();
    }
}