<?php
class Mall_center_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->logic('user_login_in_logic', 'UserLoginInLogic');
    }

    /*
     * 创建订单
     */
    public function create_order_model($data)
    {
        $sql = sql_string($data, 'insert', 'trade_base');
        $query = $this->db->query($sql);
        if ($query == true) {
            return array(
                'status' => true,
                'trade_no' => $data['trade_no'],
            );
        } else {
            return array(
                'status' => false,
                'msg' => '下单失败',
            );
        }
    }

    /*
     * 根据订单号获取订单信息
     */
    public function get_trade_order_info($trade_no)
    {
        $sql = "SELECT * FROM trade_base WHERE trade_no = '$trade_no'";
        $query = $this->db->query($sql);
        $trade_info = $query->row_array();
        if (empty($trade_info)) {
            return array(
                'status' => false,
                'msg' => '没有对应订单信息',
            );
        } else {
            return array(
                'status' => true,
                'data' => $trade_info,
            );
        }
    }

    /*
     * 支付回调更新订单状态
     */
    public function updata_trade_for_pay($trade_info)
    {
        $trade_where = [
            'trade_no' => $trade_info['trade_no'],
        ];
        $sql_trade = sql_string($trade_info, 'update', 'trade_base', $trade_where);
        $query_trade = $this->db->query($sql_trade);
        if ($query_trade == true) {
            return array(
                'status' => true,
            );
        } else {
            return array(
                'status' => false,
                'msg' => '订单信息更新失败',
            );
        }
    }

    /*
     * 获取用户订单列表
     */
    public function get_trade_list_model($data)
    {
        // 验证用户身份信息
        $account_info = $this->UserLoginInLogic->verify_login_in($data);
        if ($account_info['status'] == false) {
            return $account_info;
        }
        $account_info = $account_info['data'];
        // 判断用户是商家还是买家
        if ($account_info['title'] == 0) {
            $sql = "SELECT * FROM trade_base WHERE buyer_uid = ? LIMIT ? OFFSET ?";
        } else {
            $sql = "SELECT * FROM trade_base WHERE seller_uid = ? LIMIT ? OFFSET ?";
        }
        
        $query = $this->db->query($sql, array($data['uid'], $data['count'], $data['start']));
        $trade_list = $query->result_array();
        if (empty($trade_list)) {
            return array(
                'status' => false,
            );
        }
        return array(
            'status' => true,
            'data' => $trade_list,
        );
    }

    /*
     * 创建退款工单
     */
    public function create_refund_work_order_model($data)
    {
        $sql = sql_string($data, 'insert', 'refund_work_order');
        $query = $this->db->query($sql);
        if ($query == true) {
            return array(
                'status' => true,
                'msg' => '创建退款工单成功',
            );
        } else {
            return array(
                'status' => false,
                'msg' => '创建退款工单失败',
            );
        }
    }

    /*
     *获取退款工单列表
     */
    public function get_refund_list_model($data)
    {
        // 验证用户身份信息
        $account_info = $this->UserLoginInLogic->verify_login_in($data);
        if ($account_info['status'] == false) {
            return $account_info;
        }
        $account_info = $account_info['data'];

        // 判断用户是商家还是买家
        if ($account_info['title'] == 0) {
            $sql = "SELECT * FROM refund_work_order WHERE buyer_uid = ? LIMIT ? OFFSET ?";
        } else {
            $sql = "SELECT * FROM refund_work_order WHERE seller_uid = ? LIMIT ? OFFSET ?";
        }
        
        $query = $this->db->query($sql, array($data['uid'], $data['count'], $data['start']));
        $refund_list = $query->result_array();
        if (empty($refund_list)) {
            return array(
                'status' => false,
                'msg' => '',
            );
        }
        return array(
            'status' => true,
            'data' => $refund_list,
        );
    }

    /*
     *获取退款工单详情
     */
    public function get_refund_info_model($data)
    {
        if (isset($data['work_order_id'])) {
            $work_order_id = $data['work_order_id'];
            $sql = "SELECT * FROM refund_work_order WHERE work_order_id = $work_order_id";
        } else {
            if (!isset($data['trade_no'])) {
                return array(
                    'status' => false,
                    'msg' => '参数错误！',
                );
            }
            $trade_no = $data['trade_no'];
            $sql = "SELECT * FROM refund_work_order WHERE trade_no = '$trade_no' ORDER BY create_time DESC LIMIT 1";
        }

        $query = $this->db->query($sql);
        $refund_info = $query->row_array();
        if (empty($refund_info)) {
            return array(
                'status' => false,
                'msg' => '',
            );
        }
        return array(
            'status' => true,
            'data' => $refund_info,
        );
    }

    /*
     *商家操作退款
     */
    public function seller_active_refund_model($data)
    {
        $where = ['work_order_id' => $data['work_order_id']];
        $update = [
            'status' => $data['status'],
        ];
        $sql = sql_string($update, 'update', 'refund_work_order', $where);
        $query = $this->db->query($sql);
        if ($query == true) {
            return array(
                'status' => true,
                'msg' => '更新成功',
            );
        } else {
            return array(
                'status' => false,
                'msg' => '退款工单信息更新失败',
            );
        }
    }
}
