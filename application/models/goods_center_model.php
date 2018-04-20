<?php
class Goods_center_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /*
     * 获取商品列表
     */
    public function get_goods_list_model($data)
    {
        $start = $data['start'];
        $count = $data['count'];

        $sql = "SELECT * FROM goods_info LIMIT $count OFFSET $start";

        $query = $this->db->query($sql);
        $goods_list = $query->result_array();
        if (empty($goods_list)) {
            return array(
                'status' => false,
                'msg' => '没有相关商品',
            );
        }
        return array(
            'status' => true,
            'goods_list' => $goods_list,
        );
    }

    /*
     * 发布商品
     */
    public function push_goods_model($data, $goods_info)
    {
        $sql = sql_string($goods_info, 'insert', 'goods_info');
        // echo "<pre>"; print_r($sql); echo "</pre>";
        $query = $this->db->query($sql);
        if ($query == true) {
            return array(
                'status' => true,
                'gid' => $goods_info['gid'],
            );
        }
        return array(
            'status' => false,
            'msg' => '发布商品失败',
        );
    }
}
