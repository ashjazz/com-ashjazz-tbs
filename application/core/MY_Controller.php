<?php
class MY_Controller extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        // 实现跨域名访问
        header("Access-Control-Allow-Origin: *");
        $this->load->helper('pub_helper');
        $this->load->helper('verif_filter_helper');
    }

    protected function getPostData()
    {
        if (!isset($GLOBALS['HTTP_RAW_POST_DATA'])) {
            return [];
        }
        $data = json_decode(safe_urldecode(str_replace('data=', '', $GLOBALS['HTTP_RAW_POST_DATA'])), true);

        return $data === null ? [] : $data;
    }

    protected function getGetData()
    {
        $get_data = $this->input->get();
        return $get_data === false ? [] : $get_data;
    }

    protected function success($return_data = array(), $msg = null)
    {
        return $this->genResponse(true, $return_data, $msg);
    }

    protected function failed($msg = null, $return_data = [])
    {
        return $this->genResponse(false, [], $msg);
    }

    protected function genResponse($success, $return_data = array(), $msg = null)
    {
        if (!is_array($return_data)) {
            $msg = $return_data;
            $return_data = [];
        }
        $response = is_null($return_data) ? gen_response($success) :
            array_merge(gen_response($success), $return_data);

        if ($msg !== null) {
            $response['msg'] = $msg;
        }

        echo self::_Encrypt($response);
    }

    protected function _Encrypt($plaintext)
    {
        $response = array();
        $response["original"] = $plaintext;
        return json_encode($response);
    }
}