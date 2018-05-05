<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|    example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|    http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|    $route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|    $route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
 */

$route['default_controller'] = "welcome";
$route['test'] = "test/array_test";

/*用户中心*/
$route['sign_up'] = "user_center/sign_up"; //用户注册
$route['sign_in'] = "user_center/sign_in"; //用户登录
$route['set_user_account_info_more'] = 'user_center/set_user_account_info_more'; //用户信息完善
$route['reset_user_password'] = 'user_center/reset_user_password'; //更改密码
$route['add_receipt_info'] = "user_center/add_receipt_info"; //添加收货信息
$route['get_trade_list'] = "user_center/get_trade_list"; //获取用户订单列表

/*商品相关*/
$route['push_goods'] = "goods_center/push_goods"; //发布商品
$route['goods_list'] = "goods_center/goods_list"; //商品列表

/*商城相关*/
$route['create_order'] = "mall_center/create_order"; //创建订单
$route['pay_call_back'] = "mall_center/pay_call_back"; //支付回调
$route['trade_deliver_goods'] = "mall_center/trade_deliver_goods"; //订单发货
$route['sign_trade'] = "mall_center/sign_trade"; //签收订单
$route['cancel_trade'] = "mall_center/cancel_trade"; //用户取消订单（未付款）


$route['404_override'] = '';

/* End of file routes.php */
/* Location: ./application/config/routes.php */
