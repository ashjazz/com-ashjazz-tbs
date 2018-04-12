<?php
class Test extends CI_Controller
{

    public function index()
    {
        echo "Hello World!";
    }

    public function ash_test()
    {
        // 加载对应的model和logic类
        // $this->load->model('Blog_model', 'BlogModel');
        // $this->load->logic('Blog_logic', 'BlogLogic');
        // $this->BlogLogic->get_money();
        // $this->BlogModel->get_name();

        $data['todo_list'] = array('Clean House', 'Call Mom', 'Run Errands');
        $data['title'] = "My Real Title";
        $data['heading'] = "My Real Heading";

        $view = $this->load->view('blog_test/blogview', $data);
        // 第三个参数缺省为false 直接加载视图，当为true时，将视图返回，而不加载
    }
}
