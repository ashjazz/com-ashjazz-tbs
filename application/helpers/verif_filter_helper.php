<?php

class VerifyAndFilter {

    private $rules;
    private $data;
    private $is_filter_columns;                        //是否填充数组
    private $config;                                   //
    private $verify_result = null;
    private $verify_result_list = [];
    private $verify_result_failed_list = [];
    private $keep;        //继续执行标识字段

    public function __construct($config = [])
    {
        $this->config = array_merge([
            'default_allow_null' => true,
            'default_data_type' => 'string',
            'default_string_is_trim' => true,
            'is_filter_columns' => true,
            'check_all' => false
                ], $config);
    }

    /**
     * 新建一个对象
     */
    public static function newVerify($config = []) {
        return new self($config);
    }

    /**
     * 对象验证（键值对形式数组）
     */
    public function verifyObject(&$data, $rules) {
        !isset($this->is_filter_columns) && $this->is_filter_columns = $this->getConfig('is_filter_columns');

        $this->resetData();

        $this->execVerifyObject($data, $this->formatRules($rules));

        $this->verify_result = (empty($this->verify_result_failed_list));

        return $this;
    }

    /**
     * 数组验证
     */
    public function verifyArray(&$array_data, $rule) {
        $this->resetData();

        $res = $this->execVerifyArray($array_data, $rule);

        $this->verifyResultSave($rule, $res);

        $this->verify_result = (empty($this->verify_result_failed_list));

        return $this;
    }


    /**
     * 获取验证状态
     */
    public function getVerifyStatus() {
        return $this->verify_result;
    }

    /**
     * 获取所有的检查结果
     */
    public function getResultList() {
        return $this->verify_result_list;
    }

    /**
     * 获取检查失败的项的结果列表
     */
    public function getResultFailedList() {
        return $this->verify_result_failed_list;
    }

    /**
     * 获取验证失败的所有错误信息，
     */
    public function getFailedMsgList() {
        return getKeyList($this->verify_result_failed_list, 'msg');
    }

    /**
     * 获取第一个验证失败的项的验证结果，包含status与msg，有子规则还会存在child_result。若无任何失败项（验证成功）则为false。
     */
    public function getFirstFailedInfo() {
        reset($this->verify_result_failed_list);
        return current($this->verify_result_failed_list);
    }

    public function getFirstFailedColumn() {
        reset($this->verify_result_failed_list);
        return key($this->verify_result_failed_list);
    }
    /**
     * 获取第一个验证失败的项的错误信息。若无任何失败项（验证成功）则为false。
     * @return string|bool
     */
    public function getFirstFailedMsg() {
        $first_failed_info = $this->getFirstFailedInfo();
        return $first_failed_info === false ? false : $first_failed_info['msg'];
    }

    /**
     * 设置配置
     */
    public function setConfig($key, $val) {
        $this->config[$key] = $val;
        return $this;
    }

    /**
     * 获取配置
     */
    public function getConfig($key = null) {
        if ($key === null) {
            return $this->config;
        }
        return isset($this->config[$key]) ? $this->config[$key] : null;
    }

    private function verifyResultSave($rule, $verify_res, $column = null) {
        $verify_status = is_bool($verify_res) ? $verify_res : $verify_res->getVerifyStatus();

        $result = [
            'status' => $verify_status
        ];

        if ($verify_status == false) {
            $this->getConfig('check_all') || $this->keep = false;

            $result['msg'] = isset($rule['error_msg']) ? $rule['error_msg'] : ($column . '信息有误');
        }

        is_null($column) && $column = 0;

        if (!is_bool($verify_res)) {
            $this->verify_result_list[$column] = array_merge($result, ['child_result' => $verify_res->getResultList()]);
            if (!$verify_status) {
                $this->verify_result_failed_list[$column] = array_merge(
                    $result,
                    ['child_result' => $verify_res->getResultFailedList()]
                );
            }
        } else {
            $this->verify_result_list[$column] = $result;
            if (!$verify_status) {
                $this->verify_result_failed_list[$column] = $result;
            }
        }
    }

    private function filterExec(&$data, $rules) {
        foreach ($data as $key => $val) {
            if (!array_key_exists($key, $rules)) {
                unset($data[$key]);
            }
        }
    }

    private function execVerifyObject(&$object_data, $rules) {
        if ($this->is_filter_columns) {
            $this->filterExec($object_data, $rules);
        }

        //逐条验证
        foreach ($rules as $column => &$rule) {
            $rule['null'] = isset($rule['null']) ? boolval($rule['null']) : $this->getConfig('default_allow_null');
            !isset($rule['type']) && $rule['type'] = $this->getConfig('default_data_type');

            //参数为空字符串时，若不为string类型，则认为没有该参数
            if ($rule['type'] != 'string' && array_key_exists($column, $object_data) && $object_data[$column] === '') {
                unset($object_data[$column]);
            }

            //数据中存在该字段
            if (array_key_exists($column, $object_data)) {
                $res = $this->execVerifyValue($object_data[$column], $rules[$column]);
            } else {
                if (array_key_exists('default', $rule)) {
                    $object_data[$column] = $rule['default'];
                    $res = true;
                } elseif ($rule['null']) {
                    $res = true;
                } else {
                    $res = false;
                }
            }
            $this->verifyResultSave($rules[$column], $res, $column);
            if (!empty($this->verify_result_failed_list) && !$this->keep) {
                break;
            }
        }
        unset($rule);
    }

    private function execVerifyArray(&$arr_data, $rule) {
        if (!is_array($arr_data)) {
            return false;
        }
        $rule['null'] = isset($rule['null']) ? boolval($rule['null']) : $this->getConfig('default_allow_null');
        if (!$rule['null'] && count($arr_data) == 0) {
            return false;
        }
        $res = true;
        foreach ($arr_data as &$item_val) {
            $item_res = $this->execVerifyValue($item_val, $rule);
            $status = is_bool($item_res) ? $item_res : $item_res->getVerifyStatus();
            if ($status === false) {
                if(is_object($item_res)) {
                    $this->verify_result_failed_list[$item_res->getFirstFailedColumn()] 
                        = $item_res->getFirstFailedInfo();
                }
                $res = false;
                if (!$this->getConfig('check_all')) {
                    break;
                }
            }
        }

        return $res;
    }

    private function execVerifyValue(&$val, $rule) {
        $type = strtolower(isset($rule['type']) ? $rule['type'] : $this->getConfig('default_data_type'));
        switch ($type) {
            case 'object':
                if (!is_array($val)) {
                    return false;
                }
                if (isset($rule['rules'])) {
                    $verify = new VerifyAndFilter($this->getConfig());
                    $verify->verifyObject($val, $rule['rules']);
                    return $verify;
                }

                break;
            case 'array':
                if (!is_array($val)) {
                    return false;
                }

                if (isset($rule['rules'])) {
                    if (is_string($rule['rules'])) {
                        $item_rule = ['type' => $rule['rules']];
                    } else {
                        $item_rule = $rule['rules'];
                    }
                    $child_res = $this->execVerifyArray($val, $item_rule);

                    if ($child_res == false) {
                        return false;
                    }
                }
                break;
            case 'int':
            case 'integer':
            case 'double':
            case 'bool':
                $this->getBasicTypeVal($val, $type);
                break;
            case 'string':
                if (!is_string($val)) {
                    return false;
                }

                $this->getBasicTypeVal($val, $type);
                isset($rule['trim']) || $rule['trim'] = $this->getConfig('default_string_is_trim');
                if ($rule['trim'] !== false) {
                    $val = $rule['trim'] === true ? trim($val) : trim($val, $rule['trim']);
                }
                break;
            case 'date':
            case 'datetime':
            case 'time':
                $time = strtotime($val);
                if ($time === false) {
                    return false;
                }
                if (!empty($rule['format'])) {
                    $val = date($rule['format'], $time);
                }
                break;
        }

        if (isset($rule['min']) && $val < $rule['min']) {
            return false;
        }

        if (isset($rule['max']) && $val > $rule['max']) {
            return false;
        }

        if (isset($rule['in']) && is_array($rule['in']) && !in_array($val, $rule['in'])) {
            return false;
        }

        return true;
    }

    /**
     * 获取基本类型值（使用PHP内置函数进行转换）
     */
    private function getBasicTypeVal(&$data, $type) {
        switch ($type) {
            case 'int':
            case 'integer':
                $data = intval($data);
                break;
            case 'float':
                $data = floatval($data);
                break;
            case 'double':
                $data = doubleval($data);
                break;
            case 'string':
                $data = strval($data);
                break;
            case 'bool':
                $data = boolval($data);
                break;
            default:
                return false;
        }

        return true;
    }

    private function formatRules($rules) {
        $format_rules = [];
        foreach ($rules as $key => $rule) {
            if (!is_int($key)) {
                $rule['columns'] = $key;
            }

            if (!isset($rule['type']) && isset($rule[1])) {
                $rule['type'] = $rule[1];
                unset($rule[1]);
            }

            if (isset($rule['columns'])) {
                $columns = $rule['columns'];
                unset($rule['columns']);
            } else {
                $columns = $rule[0];
                unset($rule[0]);
            }

            //支持写法[['column', ...]] 或者 [[['column'], ...]]
            is_string($columns) && $columns = [$columns];

            foreach ($columns as $column) {
                $format_rules[$column] = isset($format_rules[$column]) ? array_merge($format_rules[$column], $rule) : $rule;
            }
        }

        return $format_rules;
    }

    private function resetData() {
        $this->verify_result_list = [];
        $this->verify_result_failed_list = [];
        $this->keep = boolval($this->config['check_all']);
    }

}
