<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Card_model extends MY_Model {

    public function __construct()
    {
        $this->set_table_name('card');

        // Call the CI_Model constructor
        parent::__construct();
    }
}