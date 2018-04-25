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
}
