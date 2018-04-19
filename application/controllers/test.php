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

    public function ash_test()
    {
        $post_data = $this->getPostData();
        $rules = [
            ['name', 'string', 'null' => false],
        ];
        $verify = VerifyAndFilter::newVerify()->verifyObject($post_data, $rules);
        if ($verify->getVerifyStatus() === false) {
            return $this->failed('验证失败，失败原因：' . ($verify->getFirstFailedMsg()));
        }
        // echo "<pre>"; print_r($get_data); echo "</pre>";
        // 加载对应的model和logic类
        $ret = $this->BlogLogic->get_money();
        $name_data = $ret['data'];
        $result_data = array(
            'data' => $name_data,
        );
        $this->success($result_data);
    }

    public function array_test()
    {
        $data = array('h','Y','yi');
        echo "<pre>"; print_r($data); echo "</pre>";
    }
}
