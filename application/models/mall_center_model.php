<?php
class Mall_center_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
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
        $sql = "SELECT * FROM trade_base WHERE buyer_uid = ? LIMIT ? OFFSET ?";
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
}
