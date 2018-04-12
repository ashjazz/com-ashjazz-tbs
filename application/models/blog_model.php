<?php
class Blog_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    public function get_name()
    {
        $sql = 'select id,name from test';
        $query = $this->db->query($sql);
        $list = $query->result_array();
        echo "<pre>"; print_r ($list); echo "</pre>";
    }
}
