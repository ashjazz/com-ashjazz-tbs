<?php
class User_center extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_center_model', 'UserCenterModel');
        $this->load->logic('user_center_logic', 'UserCenterLogic');
        $this->load->model('mall_center_model', 'MallCenterModel');
        $this->load->logic('mall_center_logic', 'MallCenterLogic');
    }

    /*
     * 用户注册控制器
     */
    public function sign_up()
    {
        $post_data = $this->getPostData();
        $rules = [
            ['mobile_phone', 'string', 'null' => false],
            ['nickname', 'string', 'null' => false],
            ['password', 'string', 'null' => false],
        ];
        $verify = VerifyAndFilter::newVerify()->verifyObject($post_data, $rules);
        if ($verify->getVerifyStatus() === false) {
            return $this->failed('验证失败，失败原因：' . ($verify->getFirstFailedMsg()));
        }

        $ret = $this->UserCenterLogic->sign_up_logic($post_data);
        $result_data = [
            'msg' => $ret['msg'],
        ];
        return $this->success($result_data);
    }

    /*
     *用户登录
     */
    public function sign_in()
    {
        $post_data = $this->getPostData();
        $rules = [
            ['mobile_phone', 'string', 'null' => false],
            ['password', 'string', 'null' => false],
        ];
        $verify = VerifyAndFilter::newVerify()->verifyObject($post_data, $rules);
        if ($verify->getVerifyStatus() === false) {
            return $this->failed('验证失败，失败原因：' . ($verify->getFirstFailedMsg()));
        }

        $ret = $this->UserCenterModel->updata_user_login_status($post_data);
        if ($ret['status'] == true) {
            $result_data = [
                'login_status' => true,
                'access_token' => $ret['access_token'],
                'uid' => $ret['uid'],
            ];
        } else {
            $result_data = [
                'login_status' => false,
                'msg' => $ret['msg'],
            ];
        }
        return $this->success($result_data);
    }

    /*
     *用户信息修改
     */
    public function set_user_account_info_more()
    {
        $post_data = $this->getPostData();
        $rules = [
            ['uid', 'int', 'null' => false],
            ['access_token', 'string', 'null' => false],
            ['location', 'string'],
            ['gender', 'int'],
            ['email', 'string'],
            ['username', 'string'],
        ];
        $verify = VerifyAndFilter::newVerify()->verifyObject($post_data, $rules);
        if ($verify->getVerifyStatus() === false) {
            return $this->failed('验证失败，失败原因：' . ($verify->getFirstFailedMsg()));
        }

        $ret = $this->UserCenterLogic->set_user_account_info_more_logic($post_data);
        if ($ret['status'] == true) {
            return $this->success('修改成功！');
        } else {
            return $this->success($ret['msg']);
        }
    }

    /*
     *用户密码修改
     */
    public function reset_user_password()
    {
        $post_data = $this->getPostData();
        $rules = [
            ['uid', 'int', 'null' => false],
            ['password_old', 'string', 'null' => false],
            ['password_new', 'string', 'null' => false],
            ['password_new_check', 'string', 'null' => false],
            ['access_token', 'string', 'null' => false],
        ];
        $verify = VerifyAndFilter::newVerify()->verifyObject($post_data, $rules);
        if ($verify->getVerifyStatus() === false) {
            return $this->failed('验证失败，失败原因：' . ($verify->getFirstFailedMsg()));
        }

        $ret = $this->UserCenterLogic->set_user_password_logic($post_data);
        if ($ret['status'] == true) {
            return $this->success('修改成功！');
        } else {
            return $this->success($ret['msg']);
        }
    }

    /*
     *添加收货信息
     */
    public function add_receipt_info()
    {
        $post_data = $this->getPostData();
        $rules = [
            ['uid', 'int', 'null' => false],
            ['receipt_address', 'string', 'null' => false],
            ['receipt_phone', 'string', 'null' => false],
            ['receipt_name', 'string', 'null' => false],
            ['access_token', 'string', 'null' => false],
        ];
        $verify = VerifyAndFilter::newVerify()->verifyObject($post_data, $rules);
        if ($verify->getVerifyStatus() === false) {
            return $this->failed('验证失败，失败原因：' . ($verify->getFirstFailedMsg()));
        }

        $ret = $this->UserCenterModel->add_receipt_info_model($post_data);
        return $this->success($ret['msg']);
    }

    /*
     *用户订单列表
     */
    public function get_trade_list()
    {
        $post_data = $this->getPostData();
        $rules = [
            ['uid', 'int', 'null' => false],
            ['start', 'int', 'default' => 0],
            ['count', 'int', 'default' => 10],
        ];
        $verify = VerifyAndFilter::newVerify()->verifyObject($post_data, $rules);
        if ($verify->getVerifyStatus() === false) {
            return $this->failed('验证失败，失败原因：' . ($verify->getFirstFailedMsg()));
        }

        $result_data = [];
        $ret = $this->MallCenterModel->get_trade_list_model($post_data);
        if ($ret['status'] == true) {
            $result_data['trade_list'] = $ret['data'];
        } else {
            return $this->success($ret['msg']);
        }
        return $this->success($result_data);
    }
}
