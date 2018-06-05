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
        $sql = "START TRANSACTION";
        $query = $this->db->query($sql);
        $buyer_uid['uid'] = $data['buyer_uid'];
        $buyer_uid['access_token'] = $data['access_token'];
        $seller_uid['uid'] = $data['seller_uid'];
        $gid = $data['gid'];

        // 买家登录校验并获取买家信息
        $account_info_buyer = $this->UserLoginInLogic->verify_login_in($buyer_uid);
        if ($account_info_buyer['status'] == false) {
            $sql = "ROLLBACK";
            $query = $this->db->query($sql);
            return array(
                'status' => false,
                'msg' => $account_info_buyer['msg'],
            );
        }
        $account_info_buyer = $account_info_buyer['data'];

        // 获取商家账户信息
        $account_info_seller = $this->UserCenterModel->is_set_account($seller_uid);
        if ($account_info_seller['status'] == false) {
            $sql = "ROLLBACK";
            $query = $this->db->query($sql);
            return array(
                'status' => false,
                'msg' => $account_info_seller['msg'],
            );
        }
        $account_info_seller = $account_info_seller['data'];

        // 获取商品信息
        $goods_info = $this->GoodsCenterModel->get_goods_info_by_gid($gid);
        if ($goods_info['status'] == false) {
            $sql = "ROLLBACK";
            $query = $this->db->query($sql);
            return array(
                'status' => false,
                'msg' => $goods_info['msg'],
            );
        }
        $goods_info = $goods_info['data'];

        if ($account_info_buyer['show_status'] == 0) {
            $sql = "ROLLBACK";
            $query = $this->db->query($sql);
            return array(
                'status' => false,
                'msg' => '用户信息无效',
            );
        }

        if ($goods_info['show_status'] != 1) {
            $sql = "ROLLBACK";
            $query = $this->db->query($sql);
            return array(
                'status' => false,
                'msg' => '商品未上架',
            );
        }

        if ($goods_info['goods_stock'] <= 0) {
            $sql = "ROLLBACK";
            $query = $this->db->query($sql);
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
        $sql = "COMMIT";
        $query = $this->db->query($sql);
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
            'msg' => '发货成功!',
        );
    }

    /*
     *用户签收订单
     */
    public function sign_trade_logic($data)
    {
        $account_info = $this->UserLoginInLogic->verify_login_in($data);
        if ($account_info['status'] == false) {
            return $account_info;
        }
        $account_info = $account_info['data'];
        $trade_info = $this->MallCenterModel->get_trade_order_info($data['trade_no']);
        if ($trade_info['status'] == false) {
            return $trade_info;
        }
        $trade_info = $trade_info['data'];

        if ($trade_info['trade_status'] != 4) {
            return array(
                'status' => false,
                'msg' => '订单不是待签收状态',
            );
        }
        $trade_info_update = [
            'trade_no' => $data['trade_no'],
            'trade_status' => 5,
        ];
        $trade_update = $this->MallCenterModel->updata_trade_for_pay($trade_info_update);
        if ($trade_update['status'] == false) {
            return array(
                'status' => false,
                'msg' => $trade_update['msg'],
            );
        }
        return array(
            'status' => true,
            'msg' => '签收成功!',
        );
    }

    /*
     *用户取消订单
     */
    public function cancel_trade_logic($data)
    {
        $account_info = $this->UserLoginInLogic->verify_login_in($data);
        if ($account_info['status'] == false) {
            return $account_info;
        }
        $account_info = $account_info['data'];

        // 获取订单信息
        $trade_info = $this->MallCenterModel->get_trade_order_info($data['trade_no']);
        if ($trade_info['status'] == false) {
            return $trade_info;
        }
        $trade_info = $trade_info['data'];
        if (in_array($trade_info['trade_status'], [3, 4])) {
            return array(
                'status' => false,
                'msg' => '该订单已付费，可退款',
            );
        }
        if (in_array($trade_info['trade_status'], [5, 9, 10])) {
            return array(
                'status' => false,
                'msg' => '该订单已完结',
            );
        }

        $trade_info_update = [
            'trade_no' => $data['trade_no'],
            'trade_status' => 9,
        ];
        $trade_update = $this->MallCenterModel->updata_trade_for_pay($trade_info_update);
        if ($trade_update['status'] == true) {
            return array(
                'status' => true,
                'msg' => '操作成功!',
            );
        } else {
            return $trade_update;
        }
    }

    /*
     *用户发起退款
     */
    public function create_refund_work_order_logic($data)
    {
        // 验证用户身份信息
        $account_info = $this->UserLoginInLogic->verify_login_in($data);
        if ($account_info['status'] == false) {
            return $account_info;
        }
        $account_info = $account_info['data'];

        $trade_info = $this->MallCenterModel->get_trade_order_info($data['trade_no']);
        if ($trade_info['status'] == false) {
            return $trade_info;
        }
        $trade_info = $trade_info['data'];
        if (!in_array($trade_info['trade_status'], [3, 4, 5, 10])) {
            return array(
                'status' => false,
                'msg' => '该订单不允许退款',
            );
        }
        $refund_recent = $this->MallCenterModel->get_refund_info_model($data);
        if ($refund_recent['status'] == true) {
            $refund_recent = $refund_recent['data'];
            if ($refund_recent['status'] == 0) {
                return [
                    'status' => false,
                    'msg' => '该订单已退款成功',
                ];
            } elseif ($refund_recent['status'] != -4) {
                return [
                    'status' => false,
                    'msg' => '该订单有未完成的退款',
                ];
            }
        }

        $create_refund = [
            'trade_no' => $data['trade_no'],
            'pay_price' => $trade_info['pay_price'],
            'buyer_uid' => $trade_info['buyer_uid'],
            'buyer_nickname' => $trade_info['buyer_nickname'],
            'seller_uid' => $trade_info['seller_uid'],
            'seller_nickname' => $trade_info['seller_nickname'],
            'product_name' => $trade_info['product_name'],
            'refund_reason_type' => $data['refund_reason_type'],
            'refund_fee' => $trade_info['pay_price'],
            'status' => 1,
            'operator_uid' => $data['uid'],
            'refund_specific_reason' => $data['refund_specific_reason'],
        ];
        !empty($data['refund_reason']) && $create_refund['refund_reason'] = $data['refund_reason'];
        if ($trade_info['status'] == 3) {
            $create_refund['trade_goods_status'] = 1;
        } elseif ($trade_info['status'] == 4) {
            $create_refund['trade_goods_status'] = 4;
        } else {
            $create_refund['trade_goods_status'] = 2;
        }

        $result_create_refund = $this->MallCenterModel->create_refund_work_order_model($create_refund);
        return $result_create_refund;
    }

    /*
     *商家操作退款
     */
    public function seller_active_refund_logic($data)
    {
        // 验证用户身份信息
        $account_info = $this->UserLoginInLogic->verify_login_in($data);
        if ($account_info['status'] == false) {
            return $account_info;
        }
        $account_info = $account_info['data'];

        // 获取退款工单详情
        $refund_info = $this->MallCenterModel->get_refund_info_model($data);
        if ($refund_info['status'] == false) {
            return $refund_info;
        }
        $refund_info = $refund_info['data'];

        if ($data['active'] == 1) {
            // 商家同意退款
            if ($refund_info['status'] != 1) {
                return [
                    'status' => false,
                    'msg' => '退款不是待审核状态',
                ];
            }
            $refund_update = [
                'work_order_id' => $data['work_order_id'],
                'status' => 3,
            ];
        } elseif ($data['active'] == 2) {
            // 商家确认退款
            if ($refund_info['status'] != 3) {
                return [
                    'status' => false,
                    'msg' => '商家未同意退款',
                ];
            }
            $refund_update = [
                'work_order_id' => $data['work_order_id'],
                'status' => 0,
            ];
            $trade_update_info = [
                'trade_no' => $refund_info['trade_no'],
                'refunded_time' => date("Y-m-d H:m:s"),
                'trade_status' => 10,
            ];
            $trade_update = $this->MallCenterModel->updata_trade_for_pay($trade_update_info);
            if ($trade_update['status'] == false) {
                return $trade_update;
            }
        } elseif ($data['active'] == 3) {
            // 商家拒绝退款
            if ($refund_info['status'] != 1) {
                return [
                    'status' => false,
                    'msg' => '退款不是待审核状态',
                ];
            }
            $refund_update = [
                'work_order_id' => $data['work_order_id'],
                'status' => -4,
            ];
        }
        $active_refund = $this->MallCenterModel->seller_active_refund_model($refund_update);
        return $active_refund;
    }
}
