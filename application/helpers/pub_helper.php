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

/**
 * 获取验证状态
 * @param $data更新、插入数据 索引数组
 * @param $type 操作类型
 * @param $table_name表名
 * @param $where条件 
 */
function sql_string($data, $type = 'insert', $table_name, $where = array())
{
    $field_array = array_keys($data);
    $value_array = array_values($data);

    switch ($type) {
        case 'insert':
            $field_string = implode(',', $field_array);
            $value_string = implode("','", $value_array);
            $sql = "INSERT INTO $table_name ($field_string) VALUES ('$value_string')";
            break;
        case 'update':
            $query_array = [];
            foreach ($data as $key => $value) {
                $query_array[] = $key . "=" . "'$value'";
            }
            $query_string = implode(',', $query_array);
            $sql = "UPDATE $table_name SET " . $query_string;
            if (!empty($where)) {
                $where_array = [];
                foreach ($where as $field => $value) {
                    $where_array[] = $field . "=" . "'$value'";
                }
                $where_string = implode(',', $where_array);
            }
            $sql = $sql . " where " . $where_string;
            break;
        default:
            # code...
            break;
    }

    return $sql;
}
