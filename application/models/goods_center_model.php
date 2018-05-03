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
        if (isset($data['key_word'])) {
            $key_word = $data['key_word'];
            $sql = "SELECT * FROM goods_info WHERE product_brandname_e like '%$key_word%' or product_name
                like '%$key_word%' LIMIT $count OFFSET $start";
        }

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

    /*
     * 根据gid获取商品信息
     */
    public function get_goods_info_by_gid($gid)
    {
        $sql = "SELECT * FROM goods_info WHERE gid = '$gid'";
        $query = $this->db->query($sql);
        $goods_info = $query->row_array();
        if (!empty($goods_info)) {
            return array(
                'status' => true,
                'data' => $goods_info,
            );
        } else {
            return array(
                'status' => false,
                'msg' => '没有相关商品',
            );
        }
    }

    /*
     * 支付回调修改商品信息
     */
    public function update_goods_info_pay($goods_info)
    {
        $where_goods = [
            'gid' => $goods_info['gid'],
        ];
        $sql_goods = sql_string($goods_info, 'update', 'goods_info', $where_goods);
        $query_goods = $this->db->query($sql_goods);
        if ($query_goods == true) {
            return array(
                'status' => true,
            );
        } else {
            return array(
                'status' => false,
                'msg' => '商品更新失败',
            );
        }
    }

}
