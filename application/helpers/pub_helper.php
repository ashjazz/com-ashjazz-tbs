<?php

function safe_urldecode($string)
{
    if (preg_match('#%[0-9A-Z]{2}#isU', $string) > 0) {
        $string = urldecode($string);
    }
    return $string;
}

function gen_response($success = true, $extra = null, $ret_code = 200)
{
    $status = ($success == true ? 'success' : 'fail');
    $response = array('updatetime' => date('Y-m-d H:i:s'), 'status' => $status, 'ret_code' => $ret_code);
    if (!empty($extra)) {
        foreach ($extra as $key => $value) {
            $response[$key] = $value;
        }

    }
    return $response;
}

function sql_string($data, $type = 'insert', $table_name)
{
    $field_array = array_keys($data);
    $value_array = array_values($data);
    $field_string = implode(',', $field_array);
    
    switch ($type) {
        case 'insert':
            $value_string = implode("','", $value_array);
            $sql = "INSERT INTO $table_name ($field_string) VALUES ('$value_string')";
            break;
        case 'update':
            
            $sql = "UPDATE $table_name SET "
        default:
            # code...
            break;
    }
    
    return $sql;
}
