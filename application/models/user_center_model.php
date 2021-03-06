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
        $title = $account_info['title'];
        if ($password_get == $password_query) {
            $access_token = date("YmdHis") . substr(microtime(), 2, 3) . rand(10, 99);
            $sql = sql_string(['access_token' => $access_token], 'update', 'user_account_info_main',
                ['mobile_phone' => $data['mobile_phone']]);
            $this->db->query($sql, array($access_token));
            return array(
                'status' => true,
                'access_token' => $access_token,
                'uid' => $uid,
                'title' => $title,
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
    public function update_user_account_info($data, $account_info_new)
    {
        $uid = $data['uid'];
        $field_string = sql_string($account_info_new, 'update', 'user_account_info_main', ['uid' => $uid]);
        $query = $this->db->query($field_string);
        if ($query == true) {
            return array(
                'status' => true,
                'msg' => '修改成功！',
            );
        } else {
            return array(
                'status' => false,
                'msg' => '修改失败，请重试',
            );
        }
    }

    /*
     *添加收货信息
     */
    public function add_receipt_info_model($data)
    {
        $account_info = $this->UserLoginInLogic->verify_login_in($data);
        if ($account_info['status'] == false) {
            return array(
                'status' => false,
                'msg' => $account_info['msg'],
            );
        } else {
            $account_info = $account_info['data'];
        }

        $receipt_info = [
            'receipt_address' => $data['receipt_address'],
            'receipt_phone' => $data['receipt_phone'],
            'receipt_name' => $data['receipt_name'],
            'uid' => $data['uid'],
        ];
        $sql = sql_string($receipt_info, 'insert', 'receipt_info');
        $query = $this->db->query($sql);
        if ($query == true) {
            return array(
                'status' => true,
                'msg' => '操作成功',
            );
        } else {
            return array(
                'status' => false,
                'msg' => '操作失败',
            );
        }
    }

    /*
     *获取收货信息
     */
    public function get_address_list_model($data)
    {
        $uid = $data['uid'];
        $sql = "SELECT * FROM receipt_info WHERE uid = $uid LIMIT 5";
        $query = $this->db->query($sql);
        $address_list = $query->result_array();
        // echo "<pre>"; print_r($address_list); echo "</pre>"; die;
        return $address_list;
    }
}
