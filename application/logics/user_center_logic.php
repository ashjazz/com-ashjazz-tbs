<?php
class User_center_logic extends MY_Logic
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_center_model', 'UserCenterModel');
    }

    /**
     * 用户注册
     *
     */
    public function sign_up_logic($data)
    {
        $account_info = $this->UserCenterModel->is_set_account($data);
        if ($account_info['status'] == true) {
            return array(
                'status' => false,
                'msg' => '该手机号已注册账号',
            );
        }
        $sign_up_info = $this->UserCenterModel->set_user_account($data);
        if ($sign_up_info['status'] == true) {
            return array(
                'status' => true,
                'msg' => '注册成功！',
            );
        }
    }

    /**
     * 用户信息修改
     *
     */
    public function set_user_account_info_more_logic($data)
    {
        $account_info_new = [];
        $account_info = $this->UserCenterModel->is_set_account($data);
        if ($account_info['status'] == false) {
            return array(
                'status' => false,
                'msg' => '没有该账号信息',
            );
        }
        isset($data['location']) && $account_info_new['location'] = $data['location'];
        isset($data['gender']) && $account_info_new['gender'] = $data['gender'];
        isset($data['email']) && $account_info_new['email'] = $data['email'];
        isset($data['username']) && $account_info_new['username'] = $data['username'];

        $ret = $this->UserCenterModel->update_user_account_info($account_info_new);
    }
}
