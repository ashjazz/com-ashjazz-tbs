<?php

class MY_Loader extends CI_Loader
{

    protected $_ci_logics = array();
    protected $_ci_logic_paths = array();

    public function __construct()
    {
        parent::__construct();
        $this->_ci_logic_paths = array(APPPATH);
    }

    public function logic($logic, $name = '', $params = null)
    {
        if (is_array($logic)) {
            foreach ($logic as $babe) {
                $this->logic($babe);
            }
            return;
        }

        if ($logic == '') {
            return;
        }

        $path = '';

        // Is the model in a sub-folder? If so, parse out the filename and path.
        if (($last_slash = strrpos($logic, '/')) !== false) {
            // The path is in front of the last slash
            $path = substr($logic, 0, $last_slash + 1);

            // And the model name behind it
            $logic = substr($logic, $last_slash + 1);
        }

        if ($name == '') {
            $name = $logic;
        }

        if (in_array($name, $this->_ci_logics, true)) {
            return;
        }

        $CI = &get_instance();
        if (isset($CI->$name)) {
            show_error('The logic name you are loading is the name of a resource that is already being used: ' . $name);
        }

        $logic = strtolower($logic);

        foreach ($this->_ci_logic_paths as $mod_path) {
            if (!file_exists($mod_path . 'logics/' . $path . $logic . '.php')) {
                continue;
            }
            if (!class_exists('MY_Logic')) {
                load_class('Logic', 'core', 'MY_');
            }
            require_once $mod_path . 'logics/' . $path . $logic . '.php';

            $logic = ucfirst($logic);

            $CI->$name = is_null($params) ? new $logic() : new $logic($params);

            $this->_ci_logics[] = $name;
            return;
        }

        // couldn't find the model
        show_error('Unable to locate the logic you have specified: ' . $logic);
    }

}
