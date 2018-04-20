<?php
class User_center_logic extends MY_Logic
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_center_model', 'UserCenterModel');
        $this->load->logic('user_login_in_logic', 'UserLoginInLogic');
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
        $verify_login = $this->UserLoginInLogic->verify_login_in($data);
        if ($verify_login['status'] == false) {
            return array(
                'status' => false,
                'msg' => $verify_login['msg'],
            );
        }
        isset($data['location']) && $account_info_new['location'] = $data['location'];
        isset($data['gender']) && $account_info_new['gender'] = $data['gender'];
        isset($data['email']) && $account_info_new['email'] = $data['email'];
        isset($data['username']) && $account_info_new['username'] = $data['username'];

        $ret = $this->UserCenterModel->update_user_account_info($data, $account_info_new);
        return $ret;
    }

    /*
     *用户密码修改
     */
    public function set_user_password_logic($data)
    {
        $verify_login = $this->UserLoginInLogic->verify_login_in($data);
        if ($verify_login['status'] == false) {
            return array(
                'status' => false,
                'msg' => $verify_login['msg'],
            );
        }
        $password_old_post = $data['password_old'];
        $password_old_real = $verify_login['account_info']['password'];
        if ($password_old_post != $password_old_real) {
            return array(
                'status' => false,
                'msg' => '旧密码错误！',
            );
        }
        $password_new = $data['password_new'];
        $password_data = [
            'password' => $password_new,
        ];
        $ret = $this->UserCenterModel->update_user_account_info($data, $password_data);
        return $ret;
    }

}
