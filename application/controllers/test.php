<?php
class Test extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        // 加载对应的model和logic类
        $this->load->logic('Blog_logic', 'BlogLogic');
        $this->load->model('Blog_model', 'BlogModel');
    }

    public function index()
    {
        echo "Hello World!";
    }

    public function ash_test()
    {
        // 加载对应的model和logic类
        $ret = $this->BlogLogic->get_money();
        $name_data = $ret['data'];
        $result_data = array(
            'code' => 200,
            'status' => 'success',
            'data' => $name_data,
        );
        print json_encode($result_data);
    }
}
