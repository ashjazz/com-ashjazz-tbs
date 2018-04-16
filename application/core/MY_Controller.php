<?php
class MY_Controller extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        // 实现跨域名访问
        header("Access-Control-Allow-Origin: *");
    }
}