<?php
class User_center_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 用户注册
     *
     */
    public function set_user_account($data)
    {
        $mobile_phone = $data['mobile_phone'];
        $nickname = $data['nickname'];
        $password = $data['password'];
        $sql = "INSERT INTO user_account_info_main (nickname,mobile_phone,password) VALUES (?,?,?)";
        $query = $this->db->query($sql, array($nickname, $mobile_phone, $password));
        if ($query == true) {
            return array(
                'status' => true,
                'msg' => '注册成功！',
            );
        } else {
            return array(
                'status' => false,
                'msg' => '注册失败！',
            );
        }

    }

    /**
     * 根据手机号查询用户信息
     *
     */
    public function is_set_account($data)
    {
        if (isset($data['mobile_phone'])) {
            $mobile_phone = $data['mobile_phone'];
            $sql = "SELECT * FROM user_account_info_main WHERE mobile_phone = ?";
            $query = $this->db->query($sql, array($mobile_phone));
        } else if (isset($data['uid'])) {
            $uid = $data['uid'];
            $sql = "SELECT * FROM user_account_info_main WHERE uid = ?";
            $query = $this->db->query($sql, array($uid));
        }
        $account_info = $query->row_array();
        if (!empty($account_info)) {
            return array(
                'status' => true,
                'data' => $account_info,
            );
        } else {
            return array(
                'status' => false,
                'msg' => '没有该账号信息',
            );
        }
    }

    /**
     * 用户登录
     *
     */
    public function updata_user_login_status($data)
    {
        $mobile_phone = $data['mobile_phone'];
        $password_get = $data['password'];
        $account_info = $this->is_set_account($data);
        if ($account_info['status'] == false) {
            return array(
                'status' => false,
                'msg' => '没有该账号信息,请注册',
            );
        }
        $account_info = $account_info['data'];
        $password_query = $account_info['password'];
        $uid = $account_info['uid'];
        if ($password_get == $password_query) {
            $access_token = date("YmdHis") . substr(microtime(), 2, 3) . rand(10, 99);
            $sql = "UPDATE user_account_info_main SET access_token = ?";
            $this->db->query($sql, array($access_token));
            return array(
                'status' => true,
                'access_token' => $access_token,
                'uid' => $uid,
            );
        } else {
            return array(
                'status' => false,
                'msg' => '密码错误,请重试!',
            );
        }

    }

    /**
     * 修改用户信息
     *
     */
    public function update_user_account_info($data)
    {
        $field_string = sql_string($data, 'insert', 'user_account_info_main');
        echo "<pre>"; print_r($field_string); echo "</pre>";
    }
}
