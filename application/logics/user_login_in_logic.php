<?php
class User_login_in_logic extends MY_Logic
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_center_model', 'UserCenterModel');
    }

    /**
     * 判断用户信息是否存在，且验证token
     *
     */
    public function verify_login_in($data)
    {
        $account_info = $this->UserCenterModel->is_set_account($data);
        if ($account_info['status'] == false) {
            return array(
                'status' => false,
                'msg' => '没有该账号信息',
            );
        }
        $access_token_post = $data['access_token'];
        $access_token_real = $account_info['data']['access_token'];
        if ($access_token_post != $access_token_real) {
            return array(
                'status' => false,
                'msg' => '请重新登陆！',
            );
        }
        return array(
            'status' => true,
            'account_info' => $account_info['data'],
        );
    }
}
