<?php
class Mall_center extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->logic('mall_center_logic', 'MallCenterLogic');
    }

    /*
     * 创建订单
     */
    public function create_order()
    {
        $post_data = $this->getPostData();
        $rules = [
            ['gid', 'string', 'null' => false],
            ['seller_uid', 'int', 'null' => false],
            ['buyer_uid', 'int', 'null' => false],
            ['buyer_nickname', 'string', 'null' => false],
            ['is_stock', 'int', 'default' => 1], //是否备货 默认已备货
            ['access_token', 'string', 'null' => false],
            ['receipt_info_id', 'int', 'null' => false], //收货信息id
        ];
        $verify = VerifyAndFilter::newVerify()->verifyObject($post_data, $rules);
        if ($verify->getVerifyStatus() === false) {
            return $this->failed('验证失败，失败原因：' . ($verify->getFirstFailedMsg()));
        }

        $ret = $this->MallCenterLogic->create_order_logic($post_data);
        $result_data = [];
        if ($ret['status'] == true) {
            $result_data['trade_no'] = $ret['trade_no'];
            return $this->success($result_data);
        } else {
            return $this->success($ret['msg']);
        }
    }

    /*
     * 支付回调
     */
    public function pay_call_back()
    {
        $post_data = $this->getPostData();
        $rules = [
            ['trade_no', 'string', 'null' => false],
            ['pay_channel', 'int', 'null' => false],
            ['payment_vouchers', 'string', 'null' => false], //支付凭证
            ['pay_price', 'int', 'null' => false],
        ];
        $verify = VerifyAndFilter::newVerify()->verifyObject($post_data, $rules);
        if ($verify->getVerifyStatus() === false) {
            return $this->failed('验证失败，失败原因：' . ($verify->getFirstFailedMsg()));
        }

        $ret = $this->MallCenterLogic->pay_call_back_logic($post_data);
        return $this->success($ret['msg']);
    }

    /*
     * 订单发货
     */
    public function trade_deliver_goods()
    {
        $post_data = $this->getPostData();
        $rules = [
            ['trade_no', 'string', 'null' => false],
        ];
        $verify = VerifyAndFilter::newVerify()->verifyObject($post_data, $rules);
        if ($verify->getVerifyStatus() === false) {
            return $this->failed('验证失败，失败原因：' . ($verify->getFirstFailedMsg()));
        }

        $ret = $this->MallCenterLogic->trade_deliver_goods_logic($post_data);
        return $this->success($ret['msg']);
    }

    /*
     *用户签收订单
     */
    public function sign_trade()
    {
        $post_data = $this->getPostData();
        $rules = [
            ['trade_no', 'string', 'null' => false],
            ['access_token', 'string', 'null' => false],
            ['uid', 'int', 'null' => false],
        ];
        $verify = VerifyAndFilter::newVerify()->verifyObject($post_data, $rules);
        if ($verify->getVerifyStatus() === false) {
            return $this->failed('验证失败，失败原因：' . ($verify->getFirstFailedMsg()));
        }

        $ret = $this->MallCenterLogic->sign_trade_logic($post_data);
        return $this->success($ret['msg']);
    }

    /*
     *用户取消订单(未支付)
     */
    public function cancel_trade()
    {
        $post_data = $this->getPostData();
        $rules = [
            ['trade_no', 'string', 'null' => false],
            ['uid', 'int', 'null' => false],
            ['access_token', 'string', 'null' => false],
        ];
        $verify = VerifyAndFilter::newVerify()->verifyObject($post_data, $rules);
        if ($verify->getVerifyStatus() === false) {
            return $this->failed('验证失败，失败原因：' . ($verify->getFirstFailedMsg()));
        }

        $ret = $this->MallCenterLogic->cancel_trade_logic($post_data);
        return $this->success($ret['msg']);
    }

    /*
     *用户发起退款
     */
    public function create_refund_work_order()
    {
        $post_data = $this->getPostData();
        $rules = [
            ['trade_no', 'string', 'null' => false],
            ['uid', 'int', 'null' => false],
            ['access_token', 'string', 'null' => false],
            ['refund_reason_type', 'int', 'default' => 1], //退款原因 1:商家原因; 2:买家原因; 3:拼团失败
            ['refund_reason', 'string'], //退款原因描述
            ['refund_specific_reason', 'int', 'default' => 1], //具体退款原因
        ];
        $verify = VerifyAndFilter::newVerify()->verifyObject($post_data, $rules);
        if ($verify->getVerifyStatus() === false) {
            return $this->failed('验证失败，失败原因：' . ($verify->getFirstFailedMsg()));
        }

        $ret = $this->MallCenterLogic->create_refund_work_order_logic($post_data);
        return $this->success($ret['msg']);
    }

    /*
     *获取退款工单详情
     */
    public function get_refund_info()
    {
        $post_data = $this->getPostData();
        $rules = [
            ['work_order_id', 'int', 'null' => false],
        ];
        $verify = VerifyAndFilter::newVerify()->verifyObject($post_data, $rules);
        if ($verify->getVerifyStatus() === false) {
            return $this->failed('验证失败，失败原因：' . ($verify->getFirstFailedMsg()));
        }

        $result_data = [];
        $ret = $this->MallCenterModel->get_refund_info_model($post_data);
        if ($ret['status'] == true) {
            $result_data['refund_info'] = $ret['data'];
        } else {
            return $this->success($ret['msg']);
        }
        return $this->success($result_data);
    }

    /*
     *商家操作退款
     */
    public function seller_active_refund()
    {
        $post_data = $this->getPostData();
        $rules = [
            ['uid', 'int', 'null' => false],
            ['work_order_id', 'int', 'null' => false],
            ['access_token', 'string', 'null' => false],
            ['active', 'int', 'null' => false, 'default' => 1], //退款操作类型 1：同意 2：确认 3：拒绝
        ];
        $verify = VerifyAndFilter::newVerify()->verifyObject($post_data, $rules);
        if ($verify->getVerifyStatus() === false) {
            return $this->failed('验证失败，失败原因：' . ($verify->getFirstFailedMsg()));
        }

        $ret = $this->MallCenterLogic->seller_active_refund_logic($post_data);
        return $this->success($ret);
    }
}
