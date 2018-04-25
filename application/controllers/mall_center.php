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
            ['pay_price', 'int', 'null' => false], //支付金额
            ['pay_channel', 'int', 'null' => false], //支付渠道
            ['buyer_nickname', 'string', 'null' => false],
            ['is_stock', 'int', 'default' => 1], //是否备货 默认已备货
            ['access_token', 'string', 'null' => false],
            ['mobile_phone', 'string', 'null' => false], //收件人手机号
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
}
