<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

/**
 * Modular Extensions - HMVC
 *
 * Adapted from the CodeIgniter Core Classes
 * @link        http://codeigniter.com
 *
 * Description:
 * This library extends the CodeIgniter CI_Loader class
 * and adds features allowing use of modules and the HMVC design pattern.
 *
 * Install this file as application/third_party/MX/Loader.php
 *
 * @copyright   Copyright (c) 2011 Wiredesignz
 * @version     5.4
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 **/
class MX_Loader extends CI_Loader
{
    protected $_module;

    public $_ci_plugins = array();
    public $_ci_cached_vars = array();

    /** Initialize the loader variables **/
    public function initialize($controller = NULL) {

        /* set the module name */
        $this->_module = CI::$APP->router->fetch_module();

        // Modified by Ivan Tcholakov, 28-SEP-2012.
        //if (is_a($controller, 'MX_Controller')) {
        if (@ is_a($controller, 'MX_Controller')) {
        //

            /* reference to the module controller */
            $this->controller = $controller;

            /* references to ci loader variables */
            foreach (get_class_vars('CI_Loader') as $var => $val) {
                if ($var != '_ci_ob_level') {
                    $this->$var =& CI::$APP->load->$var;
                }
            }

        } else {
            parent::initialize();

            /* autoload module items */
            $this->_autoloader(array());
        }

        /* add this module path to the loader variables */
        $this->_add_module_paths($this->_module);
    }

    /** Add a module path loader variables **/
    public function _add_module_paths($module = '') {

        if (empty($module)) return;

        foreach (Modules::$locations as $location => $offset) {

            /* only add a module path if it exists */
            if (is_dir($module_path = $location.$module.'/') && ! in_array($module_path, $this->_ci_model_paths))
            {
                array_unshift($this->_ci_model_paths, $module_path);
            }
        }
    }

    /** Load a module config file **/
    public function config($file = 'config', $use_sections = FALSE, $fail_gracefully = FALSE) {
        return CI::$APP->config->load($file, $use_sections, $fail_gracefully, $this->_module);
    }

    /**
     * Database Loader
     *
     * @param    mixed      $params             Database configuration options
     * @param    bool       $return             Whether to return the database object
     * @param    bool       $query_builder      Whether to enable Query Builder
     *                                          (overrides the configuration setting)
     *
     * @return   void|object|bool               Database object if $return is set to TRUE,
     *                                          FALSE on failure, void in any other case
     */
    public function database($params = '', $return = FALSE, $query_builder = NULL) {

        if (class_exists('CI_DB', FALSE) && $return === FALSE && $query_builder === NULL && isset(CI::$APP->db) && is_object(CI::$APP->db) && ! empty(CI::$APP->db->conn_id)) {
            return;
        }

        // Modified by Ivan Tcholakov, 25-DEC-2013.
        // See https://github.com/ivantcholakov/starter-public-edition-4/issues/5
        //require_once BASEPATH.'database/DB.php';
        if (file_exists(APPPATH.'database/DB.php'))
        {
            require_once APPPATH.'database/DB.php';
        }
        else
        {
            require_once BASEPATH.'database/DB.php';
        }
        //

        if ($return === TRUE) {
            return DB($params, $query_builder);
        }

        CI::$APP->db = DB($params, $query_builder);

        return CI::$APP->db;
    }

    /**
     * Load the Database Utilities Class
     *
     * @param       object      $db         Database object
     * @param       bool        $return     Whether to return the DB Forge class object or not
     * @return                              void|object
     */
    public function dbutil($db = NULL, $return = FALSE)
    {
        $CI =& get_instance();

        if ( ! is_object($db) OR ! ($db instanceof CI_DB))
        {
            class_exists('CI_DB', FALSE) OR $this->database();
            $db =& $CI->db;
        }

        // Modified by Ivan Tcholakov, 25-DEC-2013.
        // See https://github.com/ivantcholakov/starter-public-edition-4/issues/5
        //require_once(BASEPATH.'database/DB_utility.php');
        //require_once(BASEPATH.'database/drivers/'.$db->dbdriver.'/'.$db->dbdriver.'_utility.php');
        if (file_exists(APPPATH.'database/DB_utility.php'))
        {
            require_once APPPATH.'database/DB_utility.php';
        }
        else
        {
            require_once BASEPATH.'database/DB_utility.php';
        }

        if (file_exists(APPPATH.'database/drivers/'.$db->dbdriver.'/'.$db->dbdriver.'_utility.php'))
        {
            require_once APPPATH.'database/drivers/'.$db->dbdriver.'/'.$db->dbdriver.'_utility.php';
        }
        else
        {
            require_once BASEPATH.'database/drivers/'.$db->dbdriver.'/'.$db->dbdriver.'_utility.php';
        }
        //

        $class = 'CI_DB_'.$db->dbdriver.'_utility';

        if ($return === TRUE)
        {
            return new $class($db);
        }

        $CI->dbutil = new $class($db);
    }

    // --------------------------------------------------------------------

    /**
     * Load the Database Forge Class
     *
     * @param       object      $db         Database object
     * @param       bool        $return     Whether to return the DB Forge class object or not
     * @return                              void|object
     */
    public function dbforge($db = NULL, $return = FALSE)
    {
        $CI =& get_instance();
        if ( ! is_object($db) OR ! ($db instanceof CI_DB))
        {
            class_exists('CI_DB', FALSE) OR $this->database();
            $db =& $CI->db;
        }

        // Modified by Ivan Tcholakov, 25-DEC-2013.
        // See https://github.com/ivantcholakov/starter-public-edition-4/issues/5
        //require_once(BASEPATH.'database/DB_forge.php');
        //require_once(BASEPATH.'database/drivers/'.$db->dbdriver.'/'.$db->dbdriver.'_forge.php');
        if (file_exists(APPPATH.'database/DB_forge.php'))
        {
            require_once APPPATH.'database/DB_forge.php';
        }
        else
        {
            require_once BASEPATH.'database/DB_forge.php';
        }

        if (file_exists(APPPATH.'database/drivers/'.$db->dbdriver.'/'.$db->dbdriver.'_forge.php'))
        {
            require_once APPPATH.'database/drivers/'.$db->dbdriver.'/'.$db->dbdriver.'_forge.php';
        }
        else
        {
            require_once BASEPATH.'database/drivers/'.$db->dbdriver.'/'.$db->dbdriver.'_forge.php';
        }
        //

        if ( ! empty($db->subdriver))
        {
            // Modified by Ivan Tcholakov, 25-DEC-2013.
            // See https://github.com/ivantcholakov/starter-public-edition-4/issues/5
            //$driver_path = BASEPATH.'database/drivers/'.$db->dbdriver.'/subdrivers/'.$db->dbdriver.'_'.$db->subdriver.'_forge.php';
            //if (file_exists($driver_path))
            //{
            //    require_once($driver_path);
            //    $class = 'CI_DB_'.$db->dbdriver.'_'.$db->subdriver.'_forge';
            //}
            $driver_path = APPPATH.'database/drivers/'.$db->dbdriver.'/subdrivers/'.$db->dbdriver.'_'.$db->subdriver.'_forge.php';
            if (!file_exists($driver_path))
            {
                $driver_path = BASEPATH.'database/drivers/'.$db->dbdriver.'/subdrivers/'.$db->dbdriver.'_'.$db->subdriver.'_forge.php';
            }
            if (!file_exists($driver_path))
            {
                $driver_path = FALSE;
            }

            if ($driver_path !== FALSE)
            {
                require_once($driver_path);
                $class = 'CI_DB_'.$db->dbdriver.'_'.$db->subdriver.'_forge';
            }
            //
        }
        else
        {
            $class = 'CI_DB_'.$db->dbdriver.'_forge';
        }

        if ($return === TRUE)
        {
            return new $class($db);
        }

        $CI->dbforge = new $class($db);
    }

    /** Load a module helper **/
    public function helper($helper = array()) {

        if (is_array($helper)) return $this->helpers($helper);

        // Modified by Ivan Tcholakov, 12-DEC-2013.
        // See https://github.com/EllisLab/CodeIgniter/issues/2165
        //if (isset($this->_ci_helpers[$helper]))    return;
        if (isset($this->_ci_helpers[$helper])) {
            return $this;
        }
        //

        list($path, $_helper) = Modules::find($helper.'_helper', $this->_module, 'helpers/');

        // Modified by Ivan Tcholakov, 12-DEC-2013.
        // See https://github.com/EllisLab/CodeIgniter/issues/2165
        //if ($path === FALSE) return parent::helper($helper);
        if ($path === FALSE) {
            parent::helper($helper);
            return $this;
        }
        //

        Modules::load_file($_helper, $path);
        $this->_ci_helpers[$_helper] = TRUE;

        // Added by Ivan Tcholakov, 12-DEC-2013.
        // See https://github.com/EllisLab/CodeIgniter/issues/2165
        return $this;
        //
    }

    /** Load an array of helpers **/
    public function helpers($helpers = array()) {
        foreach ($helpers as $_helper) $this->helper($_helper);
        // Added by Ivan Tcholakov, 12-DEC-2013.
        // See https://github.com/EllisLab/CodeIgniter/issues/2165
        return $this;
        //
    }

    /** Load a module language file **/
    public function language($langfile = array(), $idiom = '', $return = FALSE, $add_suffix = TRUE, $alt_path = '') {
        // Modified by Ivan Tcholakov, 12-DEC-2013.
        // See https://github.com/EllisLab/CodeIgniter/issues/2165
        //return CI::$APP->lang->load($langfile, $idiom, $return, $add_suffix, $alt_path, $this->_module);
        CI::$APP->lang->load($langfile, $idiom, $return, $add_suffix, $alt_path, $this->_module);
        return $this;
        //
    }

    public function languages($languages) {
        foreach($languages as $_language) $this->language($_language);
        // Added by Ivan Tcholakov, 12-DEC-2013.
        // See https://github.com/EllisLab/CodeIgniter/issues/2165
        return $this;
        //
    }

    /** Load a module library **/
    public function library($library = '', $params = NULL, $object_name = NULL) {

        if (is_array($library)) return $this->libraries($library);

        $class = strtolower(basename($library));

        // Modified by Ivan Tcholakov, 26-JUL-2013.
        //if (isset($this->_ci_classes[$class]) AND $_alias = $this->_ci_classes[$class])
        //    return CI::$APP->$_alias;
        //
        //($_alias = strtolower($object_name)) OR $_alias = $class;
        if (isset($this->_ci_classes[$class])) {

            $_alias = $this->_ci_classes[$class];

            // Modified by Ivan Tcholakov, 12-DEC-2013.
            // Total mystery is here.
            // The test case: Load parsers with different drivers (the default and mustache).
            //if ($_alias) {
            //if ($_alias && $_alias != $class) {
            if ($_alias && $_alias != $class && $class != 'parser') {   // A durty workaround for parser drivers, 20-DEC-2013.
            //
                // Modified by Ivan Tcholakov, 12-DEC-2013.
                // See https://github.com/EllisLab/CodeIgniter/issues/2165
                //return CI::$APP->$_alias;
                return $this;
                //
            }
        }

        $_alias = strtolower($object_name);

        if (!$_alias) {

            $_alias = $class;
        }
        //

        list($path, $_library) = Modules::find($library, $this->_module, 'libraries/');

        /* load library config file as params */
        if ($params == NULL) {
            list($path2, $file) = Modules::find($_alias, $this->_module, 'config/');
            ($path2) AND $params = Modules::load_file($file, $path2, 'config');
        }

        if ($path === FALSE) {

            $this->_ci_load_class($library, $params, $object_name);
            $_alias = $this->_ci_classes[$class];

        } else {

            Modules::load_file($_library, $path);

            $library = ucfirst($_library);
            CI::$APP->$_alias = new $library($params);

            $this->_ci_classes[$class] = $_alias;
        }

        // Modified by Ivan Tcholakov, 12-DEC-2013.
        // See https://github.com/EllisLab/CodeIgniter/issues/2165
        //return CI::$APP->$_alias;
        return $this;
        //
    }

    /** Load an array of libraries **/
    public function libraries($libraries) {
        foreach ($libraries as $_library) $this->library($_library);
        // Added by Ivan Tcholakov, 12-DEC-2013.
        // See https://github.com/EllisLab/CodeIgniter/issues/2165
        return $this;
        //
    }

    /** Load a module model **/
    public function model($model, $object_name = NULL, $connect = FALSE) {

        if (is_array($model)) return $this->models($model);

        ($_alias = $object_name) OR $_alias = basename($model);

        if (in_array($_alias, $this->_ci_models, TRUE)) {
            // Modified by Ivan Tcholakov, 12-DEC-2013.
            // See https://github.com/EllisLab/CodeIgniter/issues/2165
            //return CI::$APP->$_alias;
            return $this;
            //
        }

        /* check module */
        list($path, $_model) = Modules::find(strtolower($model), $this->_module, 'models/');

        if ($path == FALSE) {

            /* check application & packages */
            // Modified by Ivan Tcholakov, 30-OCT-2013.
            //parent::model($model, $object_name, $connect);
            $this->_ci_model($model, $object_name, $connect);
            //

        } else {

            class_exists('CI_Model', FALSE) OR load_class('Model', 'core');

            if ($connect !== FALSE AND ! class_exists('CI_DB', FALSE)) {
                if ($connect === TRUE) $connect = '';
                $this->database($connect, FALSE, TRUE);
            }

            Modules::load_file($_model, $path);

            $model = ucfirst($_model);
            CI::$APP->$_alias = new $model();

            $this->_ci_models[] = $_alias;
        }

        // Modified by Ivan Tcholakov, 12-DEC-2013.
        // See https://github.com/EllisLab/CodeIgniter/issues/2165
        //return CI::$APP->$_alias;
        return $this;
        //
    }

    // Added by Ivan Tcholakov, 30-OCT-2013.
    protected function _ci_model($model, $name = '', $db_conn = FALSE)
    {
        if (empty($model))
        {
            return;
        }
        elseif (is_array($model))
        {
            foreach ($model as $key => $value)
            {
                $this->model(is_int($key) ? $value : $key, $value);
            }
            return;
        }

        $path = '';

        // Is the model in a sub-folder? If so, parse out the filename and path.
        if (($last_slash = strrpos($model, '/')) !== FALSE)
        {
            // The path is in front of the last slash
            $path = substr($model, 0, ++$last_slash);

            // And the model name behind it
            $model = substr($model, $last_slash);
        }

        if (empty($name))
        {
            $name = $model;
        }

        if (in_array($name, $this->_ci_models, TRUE))
        {
            return;
        }

        $CI =& get_instance();
        if (isset($CI->$name))
        {
            show_error('The model name you are loading is the name of a resource that is already being used: '.$name);
        }

        if ($db_conn !== FALSE && ! class_exists('CI_DB', FALSE))
        {
            if ($db_conn === TRUE)
            {
                $db_conn = '';
            }

            $CI->load->database($db_conn, FALSE, TRUE);
        }

        if ( ! class_exists('CI_Model', FALSE))
        {
            load_class('Model', 'core');
        }

        $model = ucfirst(strtolower($model));

        foreach ($this->_ci_model_paths as $mod_path)
        {
            if ( ! file_exists($mod_path.'models/'.$path.$model.'.php'))
            {
                if (file_exists($mod_path.'models/'.$path.lcfirst($model).'.php'))
                {
                    $model = lcfirst($model);
                }
                else
                {
                    continue;
                }
            }

            require_once($mod_path.'models/'.$path.$model.'.php');

            // Added by Ivan Tcholakov, 25-JUL-2013.
            $model = ucfirst($model);
            //
            $CI->$name = new $model();
            $this->_ci_models[] = $name;
            return;
        }

        // couldn't find the model
        show_error('Unable to locate the model you have specified: '.$model);
    }

    /** Load an array of models **/
    public function models($models) {
        foreach ($models as $_model) $this->model($_model);
        // Added by Ivan Tcholakov, 12-DEC-2013.
        // See https://github.com/EllisLab/CodeIgniter/issues/2165
        return $this;
        //
    }

    /** Load a module controller **/
    public function module($module, $params = NULL) {

        if (is_array($module)) return $this->modules($module);

        $_alias = strtolower(basename($module));
        CI::$APP->$_alias = Modules::load(array($module => $params));
        // Modified by Ivan Tcholakov, 12-DEC-2013.
        // See https://github.com/EllisLab/CodeIgniter/issues/2165
        //return CI::$APP->$_alias;
        return $this;
        //
    }

    /** Load an array of controllers **/
    public function modules($modules) {
        foreach ($modules as $_module) $this->module($_module);
        // Added by Ivan Tcholakov, 12-DEC-2013.
        // See https://github.com/EllisLab/CodeIgniter/issues/2165
        return $this;
        //
    }

    /** Load a module view **/
    // Modified by Ivan Tcholakov, 27-DEC-2013.
    //public function view($view, $vars = array(), $return = FALSE) {
    public function view($view, $vars = array(), $return = FALSE, $parsers = array()) {
    //

        list($path, $_view) = Modules::find($view, $this->_module, 'views/');

        if ($path != FALSE) {
            $this->_ci_view_paths = array($path => TRUE) + $this->_ci_view_paths;
            $view = $_view;
        }

        // Modified by Ivan Tcholakov, 12-DEC-2013, 27-DEC-2013.
        // See https://github.com/EllisLab/CodeIgniter/issues/2165
        //return $this->_ci_load(array('_ci_view' => $view, '_ci_vars' => $this->_ci_object_to_array($vars), '_ci_return' => $return));
        if ($return) {
            return $this->_ci_load(array('_ci_view' => $view, '_ci_vars' => $this->_ci_object_to_array($vars), '_ci_return' => $return, '_ci_parsers_param' => $parsers));
        }
        $this->_ci_load(array('_ci_view' => $view, '_ci_vars' => $this->_ci_object_to_array($vars), '_ci_return' => $return, '_ci_parsers_param' => $parsers));
        return $this;
        //
    }

    public function _ci_is_instance() {}

    protected function &_ci_get_component($component) {
        return CI::$APP->$component;
    }

    public function __get($class) {
        return (isset($this->controller)) ? $this->controller->$class : CI::$APP->$class;
    }

    public function _ci_load($_ci_data) {

        extract($_ci_data);

        if (isset($_ci_view)) {

            $_ci_path = '';

            /* add file extension if not provided */
            $_ci_file = (pathinfo($_ci_view, PATHINFO_EXTENSION)) ? $_ci_view : $_ci_view.'.php';

            foreach ($this->_ci_view_paths as $path => $cascade) {
                if (file_exists($view = $path.$_ci_file)) {
                    $_ci_path = $view;
                    break;
                }

                if ( ! $cascade) break;
            }

        } elseif (isset($_ci_path)) {

            $_ci_file = basename($_ci_path);
            if( ! file_exists($_ci_path)) $_ci_path = '';
        }

        if (empty($_ci_path))
            show_error('Unable to load the requested file: '.$_ci_file);

        if (isset($_ci_vars))
            $this->_ci_cached_vars = array_merge($this->_ci_cached_vars, (array) $_ci_vars);

        extract($this->_ci_cached_vars);

        // Added by Ivan Tcholakov, 27-DEC-2013.

        $_ci_parsers_param = isset($_ci_parsers_param) ? $_ci_parsers_param : array();

        if (!is_array($_ci_parsers_param)) {

            $_ci_parsers_param = (string) $_ci_parsers_param;

            if ($_ci_parsers_param != '') {
                $_ci_parsers_param = array($_ci_parsers_param);
            } else {
                $_ci_parsers_param = array();
            }
        }

        $_ci_parsers = array();

        foreach ($_ci_parsers_param as $_ci_parser_key => $_ci_parser_value) {

            if (is_string($_ci_parser_key)) {

                $_ci_parsers[] = array('parser' => $_ci_parser_key, 'config' => $_ci_parser_value);

            } elseif (is_string($_ci_parser_value)) {

                $_ci_parsers[] = array('parser' => $_ci_parser_value, 'config' => array());
            }

        }
        //

        ob_start();

        if (empty($_ci_parsers)) {

            if (!is_php('5.4') && (bool) @ini_get('short_open_tag') === FALSE && CI::$APP->config->item('rewrite_short_tags') == TRUE && function_usable('eval')) {
                echo eval('?>'.preg_replace("/;*\s*\?>/", "; ?>", str_replace('<?=', '<?php echo ', file_get_contents($_ci_path))));
            } else {
                include($_ci_path);
            }

        } else {

            // This conditional branch has been added by Ivan Tcholakov, 27-DEC-2013.

            if (!isset($_ci_vars)) {
                $_ci_vars = array();
            }

            ob_start();

            if (!is_php('5.4') && (bool) @ini_get('short_open_tag') === FALSE && CI::$APP->config->item('rewrite_short_tags') == TRUE && function_usable('eval')) {
                echo eval('?>'.preg_replace("/;*\s*\?>/", "; ?>", str_replace('<?=', '<?php echo ', file_get_contents($_ci_path))));
            } else {
                include($_ci_path);
            }

            $_ci_template_content = ob_get_clean();

            foreach ($_ci_parsers as $_ci_parser)
            {
                CI::$APP->load->parser($_ci_parser['parser']);
                $_ci_template_content = CI::$APP->{$_ci_parser['parser']}->parse_string($_ci_template_content, $_ci_vars, true, $_ci_parser['config']);
            }

            echo $_ci_template_content;
        }

        log_message('debug', 'File loaded: '.$_ci_path);

        if ($_ci_return == TRUE) return ob_get_clean();

        if (ob_get_level() > $this->_ci_ob_level + 1) {
            ob_end_flush();
        } else {
            CI::$APP->output->append_output(ob_get_clean());
        }
    }

    /** Autoload module items **/
    public function _autoloader($autoload) {

        $path = FALSE;

        if ($this->_module) {

            list($path, $file) = Modules::find('constants', $this->_module, 'config/');

            /* module constants file */
            if ($path != FALSE) {
                include_once $path.$file.'.php';
            }

            list($path, $file) = Modules::find('autoload', $this->_module, 'config/');

            /* module autoload file */
            if ($path != FALSE) {
                $autoload = array_merge(Modules::load_file($file, $path, 'autoload'), $autoload);
            }
        }

        /* nothing to do */
        if (count($autoload) == 0) return;

        /* autoload package paths */
        if (isset($autoload['packages'])) {
            foreach ($autoload['packages'] as $package_path) {
                $this->add_package_path($package_path);
            }
        }

        /* autoload config */
        if (isset($autoload['config'])) {
            foreach ($autoload['config'] as $config) {
                $this->config($config);
            }
        }

        /* autoload helpers, plugins, languages */
        foreach (array('helper', 'plugin', 'language') as $type) {
            if (isset($autoload[$type])) {
                foreach ($autoload[$type] as $item) {
                    $this->$type($item);
                }
            }
        }

        /* autoload database & libraries */
        if (isset($autoload['libraries'])) {
            if (in_array('database', $autoload['libraries'])) {
                /* autoload database */
                if ( ! $db = CI::$APP->config->item('database')) {
                    $db['params'] = 'default';
                    $db['active_record'] = TRUE;
                }
                $this->database($db['params'], FALSE, $db['active_record']);
                $autoload['libraries'] = array_diff($autoload['libraries'], array('database'));
            }

            /* autoload libraries */
            foreach ($autoload['libraries'] as $library) {
                $this->library($library);
            }
        }

        /* autoload models */
        if (isset($autoload['model'])) {
            foreach ($autoload['model'] as $model => $alias) {
                (is_numeric($model)) ? $this->model($alias) : $this->model($model, $alias);
            }
        }

        /* autoload module controllers */
        if (isset($autoload['modules'])) {
            foreach ($autoload['modules'] as $controller) {
                ($controller != $this->_module) AND $this->module($controller);
            }
        }
    }
}

/** load the CI class for Modular Separation **/
(class_exists('CI', FALSE)) OR require dirname(__FILE__).'/Ci.php';
