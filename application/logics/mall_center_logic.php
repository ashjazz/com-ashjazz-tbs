<?php
class Mall_center_logic extends MY_Logic
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_center_model', 'UserCenterModel');
        $this->load->model('goods_center_model', 'GoodsCenterModel');
        $this->load->logic('user_login_in_logic', 'UserLoginInLogic');
        $this->load->model('mall_center_model', 'MallCenterModel');
    }

    /*
     * 创建订单
     */
    public function create_order_logic($data)
    {
        $buyer_uid['uid'] = $data['buyer_uid'];
        $buyer_uid['access_token'] = $data['access_token'];
        $seller_uid['uid'] = $data['seller_uid'];
        $gid = $data['gid'];

        // 买家登录校验并获取买家信息
        $account_info_buyer = $this->UserLoginInLogic->verify_login_in($buyer_uid);
        if ($account_info_buyer['status'] == false) {
            return array(
                'status' => false,
                'msg' => $account_info_buyer['msg'],
            );
        }
        $account_info_buyer = $account_info_buyer['data'];

        // 获取商家账户信息
        $account_info_seller = $this->UserCenterModel->is_set_account($seller_uid);
        if ($account_info_seller['status'] == false) {
            return array(
                'status' => false,
                'msg' => $account_info_seller['msg'],
            );
        }
        $account_info_seller = $account_info_seller['data'];

        // 获取商品信息
        $goods_info = $this->GoodsCenterModel->get_goods_info_by_gid($gid);
        if ($goods_info['status'] == false) {
            return array(
                'status' => false,
                'msg' => $goods_info['msg'],
            );
        }
        $goods_info = $goods_info['data'];

        if ($account_info_buyer['show_status'] == 0) {
            return array(
                'status' => false,
                'msg' => '用户信息无效',
            );
        }

        if ($goods_info['show_status'] != 1) {
            return array(
                'status' => false,
                'msg' => '商品未上架',
            );
        }

        if ($goods_info['goods_stock'] <= 0) {
            return array(
                'status' => false,
                'msg' => '商品库存不足',
            );
        }

        $trade_no = 't' . date("YmdHis") . substr(microtime(), 2, 3) . rand(10, 99);
        $order_info = [
            'trade_no' => $trade_no,
            'gid' => $goods_info['gid'],
            'seller_uid' => $account_info_seller['uid'],
            'buyer_uid' => $account_info_buyer['uid'],
            'goods_price' => $goods_info['goods_price'],
            'product_brandname_e' => $goods_info['product_brandname_e'],
            'product_name' => $goods_info['product_name'],
            'product_cover_image' => $goods_info['product_cover_image'],
            'trade_status' => 2,
            'seller_nickname' => $account_info_seller['nickname'],
            'buyer_nickname' => $account_info_buyer['nickname'],
            'goods_imgs' => $goods_info['goods_imgs'],
            'is_stock' => $data['is_stock'],
            'buyer_mobilephone' => $account_info_buyer['mobile_phone'],
            'trade_price' => $goods_info['goods_price'],
            'receipt_info_id' => $data['receipt_info_id'],
        ];

        $ret = $this->MallCenterModel->create_order_model($order_info);
        return $ret;
    }

    /*
     * 支付回调
     */
    public function pay_call_back_logic($data)
    {
        // 获取订单相关信息
        $trade_info = $this->MallCenterModel->get_trade_order_info($data['trade_no']);
        if ($trade_info['status'] == false) {
            return array(
                'status' => false,
                'msg' => $trade_info['msg'],
            );
        } else {
            $trade_info = $trade_info['data'];
        }
        $gid = $trade_info['gid'];

        // 获取商品信息
        $goods_info = $this->GoodsCenterModel->get_goods_info_by_gid($gid);
        if ($goods_info['status'] == false) {
            return array(
                'status' => false,
                'msg' => $goods_info['msg'],
            );
        } else {
            $goods_info = $goods_info['data'];
        }

        // 获取商品库存
        $goods_stock = $goods_info['goods_stock'];
        $goods_stock_new = $goods_stock - 1;

        if ($trade_info['trade_status'] != 2) {
            return array(
                'status' => false,
                'msg' => '订单不是待付款状态',
            );
        }
        $pay_time = date("Y-m-d H:m:s", time());

        // 更新订单以及商品
        $trade_info_update = [
            'trade_no' => $data['trade_no'],
            'pay_price' => $data['pay_price'],
            'pay_channel' => $data['pay_channel'],
            'trade_status' => 3,
            'pay_time' => $pay_time,
            'payment_vouchers' => $data['payment_vouchers'],
        ];
        $trade_update = $this->MallCenterModel->updata_trade_for_pay($trade_info_update);
        if ($trade_update['status'] == false) {
            return array(
                'status' => false,
                'msg' => $trade_update['msg'],
            );
        }

        // 商品信息库存修改
        // $goods_info_update = [
        //     'sales' => $goods_info['sales'] + 1,
        //     'goods_stock' => $goods_stock_new,
        //     'gid' => $gid,
        // ];
        // $goods_update = $this->GoodsCenterModel->update_goods_info_pay($goods_info_update);
        // if ($goods_update['status'] == false) {
        //     return array(
        //         'status' => false,
        //         'msg' => $goods_update['msg'],
        //     );
        // }
        return array(
            'status' => false,
            'msg' => '回调成功！',
        );
    }

    /*
     * 订单发货
     */
    public function trade_deliver_goods_logic($data)
    {
        $trade_info = $this->MallCenterModel->get_trade_order_info($data['trade_no']);
        if ($trade_info['status'] == false) {
            return $trade_info;
        }
        $trade_info = $trade_info['data'];

        $goods_info = $this->GoodsCenterModel->get_goods_info_by_gid($trade_info['gid']);
        if ($goods_info['status'] == false) {
            return $goods_info;
        }
        $goods_info = $goods_info['data'];
        if ($trade_info['trade_status'] != 3) {
            return array(
                'status' => false,
                'msg' => '订单不是待发货状态',
            );
        }
        if ($goods_info['goods_stock'] <= 0) {
            return array(
                'status' => false,
                'msg' => '商品库存不足',
            );
        }

        $gid = $trade_info['gid'];
        $goods_stock_new = $goods_info['goods_stock'] - 1;
        $trade_info_update = [
            'trade_no' => $data['trade_no'],
            'trade_status' => 4,
        ];
        $trade_update = $this->MallCenterModel->updata_trade_for_pay($trade_info_update);
        if ($trade_update['status'] == false) {
            return array(
                'status' => false,
                'msg' => $trade_update['msg'],
            );
        }

        // 商品信息库存修改
        $goods_info_update = [
            'sales' => $goods_info['sales'] + 1,
            'goods_stock' => $goods_stock_new,
            'gid' => $gid,
        ];
        $goods_update = $this->GoodsCenterModel->update_goods_info_pay($goods_info_update);
        if ($goods_update['status'] == false) {
            return array(
                'status' => false,
                'msg' => $goods_update['msg'],
            );
        }
        return array(
            'status' => true,
            '发货成功!',
        );
    }
}
