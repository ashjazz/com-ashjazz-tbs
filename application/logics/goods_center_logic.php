<?php
class Goods_center_logic extends MY_Logic
{
    public function __construct()
    {
        parent::__construct();
        $this->load->logic('user_login_in_logic', 'UserLoginInLogic');
        $this->load->model('goods_center_model', 'GoodsCenterModel');
    }

    /*
     * 发布商品
     */
    public function push_goods_logic($data)
    {
        $goods_info = [];
        $verify_login = $this->UserLoginInLogic->verify_login_in($data);
        if ($verify_login['status'] == false) {
            return array(
                'status' => false,
                'msg' => $verify_login['msg'],
            );
        }
        if ($verify_login['data']['title'] == 0) {
            return array(
                'status' => false,
                'msg' => '该用户不是商家！',
            );
        }
        $goods_info['seller_uid'] = $data['uid'];
        $goods_info['price'] = $data['price'];
        $goods_info['product_brandname_e'] = $data['product_brandname_e'];
        $goods_info['product_name'] = $data['product_name'];
        $goods_info['product_cover_image'] = $data['product_cover_image'];
        isset($data['goods_description']) && $goods_info['goods_description'] = $data['goods_description'];
        isset($data['product_desc']) && $goods_info['product_desc'] = $data['product_desc'];
        $goods_info['goods_stock'] = $data['goods_stock'];
        isset($data['goods_imgs']) && $goods_info['goods_imgs'] = $data['goods_imgs'];
        $goods_info['goods_price'] = $data['goods_price'];
        $goods_info['goods_return_support'] = $data['goods_return_support'];
        $goods_info['goods_type'] = $data['goods_type'];
        $goods_info['show_status'] = 1;
        $goods_info['seller_nickname'] = $verify_login['account_info']['nickname'];

        $gid = 'g' . date("Ymd") . rand(10, 99);
        $goods_info['gid'] = $gid;

        $ret = $this->GoodsCenterModel->push_goods_model($data, $goods_info);
        return $ret;
    }
}
