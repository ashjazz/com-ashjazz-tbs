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
    }
}
