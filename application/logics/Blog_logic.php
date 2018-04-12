<?php
class Blog_logic extends MY_Logic
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Blog_model', 'BlogModel');
    }

    public function get_money()
    {
        $this->BlogModel->get_name();
        // echo "No money for you!";
    }
}