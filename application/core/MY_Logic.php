<?php
class MY_Logic
{
    public function __construct()
    {}

    public function __get($key)
    {
        $CI = &get_instance();
        return $CI->$key;
    }
}
