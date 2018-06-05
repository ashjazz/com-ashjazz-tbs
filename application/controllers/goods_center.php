<?php
class Goods_center extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->logic('goods_center_logic', 'GoodsCenterLogic');
        $this->load->model('goods_center_model', 'GoodsCenterModel');
    }

    /*
     * 获取商品列表
     */
    public function goods_list()
    {
        $post_data = $this->getPostData();
        $rules = [
            ['start', 'int', 'default' => 0],
            ['count', 'int', 'default' => 12],
            ['key_word', 'string'],
            ['goods_type', 'int', 'default' => 1],
        ];
        $verify = VerifyAndFilter::newVerify()->verifyObject($post_data, $rules);
        if ($verify->getVerifyStatus() === false) {
            return $this->failed('验证失败，失败原因：' . ($verify->getFirstFailedMsg()));
        }

        $result_data = [];
        $ret = $this->GoodsCenterModel->get_goods_list_model($post_data);
        if ($ret['status'] == false) {
            return $this->success($ret['msg']);
        } else {
            $result_data['goods_list'] = $ret['goods_list'];
            return $this->success($result_data);
        }
    }

    /*
     * 发布商品
     */
    public function push_goods()
    {
        $post_data = $this->getPostData();
        $rules = [
            ['uid', 'int', 'null' => false],
            ['access_token', 'string', 'null' => false],
            ['price', 'int', 'null' => false],
            ['product_brandname_e', 'string', 'null' => false],
            ['product_name', 'string', 'null' => false],
            ['product_cover_image', 'string', 'null' => false],
            ['goods_description', 'string'],
            ['product_desc', 'string'],
            ['goods_stock', 'int', 'null' => false],
            ['goods_imgs', 'string'],
            ['goods_price', 'int', 'null' => false],
            ['goods_return_support', 'int', 'in' => [0, 1], 'default' => 1],
            ['goods_type', 'int', 'null' => false], //商品类目 1:外套 2：上衣 3：裤装 4：鞋靴 5：配件
        ];
        $verify = VerifyAndFilter::newVerify()->verifyObject($post_data, $rules);
        if ($verify->getVerifyStatus() === false) {
            return $this->failed('验证失败，失败原因：' . ($verify->getFirstFailedMsg()));
        }

        $ret = $this->GoodsCenterLogic->push_goods_logic($post_data);
        $result_data = [];
        if ($ret['status'] == true) {
            $result_data['gid'] = $ret['gid'];
            return $this->success($result_data);
        } else {
            return $this->success($ret['msg']);
        }
    }

    /*
     * 获取商品详情
     */
    public function get_goods_info()
    {
        $post_data = $this->getPostData();
        $rules = [
            ['gid', 'string', 'null' => false],
        ];
        $verify = VerifyAndFilter::newVerify()->verifyObject($post_data, $rules);
        if ($verify->getVerifyStatus() === false) {
            return $this->failed('验证失败，失败原因：' . ($verify->getFirstFailedMsg()));
        }

        $ret = $this->GoodsCenterModel->get_goods_info_by_gid($post_data['gid']);
        if ($ret['status'] == true) {
            return $this->success(['goods_info' => $ret['data']]);
        } else {
            return $this->success($ret['msg']);
        }
    }
}
