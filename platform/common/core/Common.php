<?php defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('get_config'))
{
    /**
     * Loads the main config.php file
     *
     * This function lets us grab the config file even if the Config class
     * hasn't been instantiated yet
     *
     * @param     array
     * @return    array
     */
    function &get_config(Array $replace = array())
    {
        static $config;

        // Added by Ivan Tcholakov, 13-OCT-2013.
        global $DETECT_URL;
        //

        if (empty($config))
        {
            // Added by Ivan Tcholakov, 02-OCT-2013.
            // Loading the common configuration file first.
            $file_path = COMMONPATH.'config/config.php';
            $found = FALSE;
            if (file_exists($file_path))
            {
                $found = TRUE;
                require($file_path);
            }
            if (file_exists($file_path = COMMONPATH.'config/'.ENVIRONMENT.'/config.php'))
            {
                require($file_path);
            }
            //

            $file_path = APPPATH.'config/config.php';
            // Removed by Ivan Tcholakov, 02-OCT-2013.
            //$found = FALSE;
            //
            if (file_exists($file_path))
            {
                $found = TRUE;
                require($file_path);
            }

            // Is the config file in the environment folder?
            if (file_exists($file_path = APPPATH.'config/'.ENVIRONMENT.'/config.php'))
            {
                require($file_path);
            }
            elseif ( ! $found)
            {
                set_status_header(503);
                echo 'The configuration file does not exist.';
                exit(3); // EXIT_CONFIG
            }

            // Does the $config array exist in the file?
            if ( ! isset($config) OR ! is_array($config))
            {
                set_status_header(503);
                echo 'Your config file does not appear to be formatted correctly.';
                exit(3); // EXIT_CONFIG
            }
        }

        // Are any values being dynamically added or replaced?
        foreach ($replace as $key => $val)
        {
            $config[$key] = $val;
        }

        return $config;
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('load_class'))
{
    /**
     * Class registry
     *
     * This function acts as a singleton. If the requested class does not
     * exist it is instantiated and set to a static variable. If it has
     * previously been instantiated the variable is returned.
     *
     * @param       string      the class name being requested
     * @param       string      the directory where the class should be found
     * @param       string      an optional argument to pass to the class constructor
     * @return      object
     */
    function &load_class($class, $directory = 'libraries', $param = NULL)
    {
        static $_classes = array();

        // Does the class exist? If so, we're done...
        if (isset($_classes[$class]))
        {
            return $_classes[$class];
        }

        $name = FALSE;

        // Look for the class first in the local application/libraries folder
        // then in the native system/libraries folder
        foreach (array(APPPATH, BASEPATH) as $path)
        {
            if (file_exists($path.$directory.'/'.$class.'.php'))
            {
                $name = 'CI_'.$class;

                if (!class_exists($name, FALSE))
                {
                    require_once $path.$directory.'/'.$class.'.php';
                }

                break;
            }
        }

        // Added by Ivan Tcholakov, 11-OCT-2013.
        // Load customized core classes.
        if (file_exists(COMMONPATH."$directory/Core_$class.php"))
        {
            $name = 'Core_'.$class;

            if (!class_exists($name, FALSE))
            {
                require_once COMMONPATH."$directory/Core_$class.php";
            }
        }
        //

        // Is the request a class extension? If so we load it too
        if (file_exists(APPPATH.$directory.'/'.config_item('subclass_prefix').$class.'.php'))
        {
            $name = config_item('subclass_prefix').$class;

            if (!class_exists($name, FALSE))
            {
                require_once APPPATH.$directory.'/'.config_item('subclass_prefix').$class.'.php';
            }
        }

        // Did we find the class?
        if ($name === FALSE)
        {
            // Note: We use exit() rather then show_error() in order to avoid a
            // self-referencing loop with the Exceptions class
            set_status_header(503);
            echo 'Unable to locate the specified class: '.$class.'.php';
            exit(5); // EXIT_UNK_CLASS
        }

        // Keep track of what we just loaded
        is_loaded($class);

        $_classes[$class] = new $name();
        return $_classes[$class];
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('get_mimes'))
{
    /**
     * Returns the MIME types array from config/mimes.php
     *
     * @return    array
     */
    function &get_mimes()
    {
        static $_mimes = array();

        if (empty($_mimes))
        {
            if (file_exists(APPPATH.'config/'.ENVIRONMENT.'/mimes.php'))
            {
                $_mimes = include(APPPATH.'config/'.ENVIRONMENT.'/mimes.php');
            }
            elseif (file_exists(APPPATH.'config/mimes.php'))
            {
                $_mimes = include(APPPATH.'config/mimes.php');
            }
            elseif (file_exists(COMMONPATH.'config/'.ENVIRONMENT.'/mimes.php'))
            {
                $_mimes = include(COMMONPATH.'config/'.ENVIRONMENT.'/mimes.php');
            }
            elseif (file_exists(COMMONPATH.'config/mimes.php'))
            {
                $_mimes = include(COMMONPATH.'config/mimes.php');
            }
        }

        return $_mimes;
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('_error_handler'))
{
    /**
     * Error Handler
     *
     * This is the custom error handler that is declared at the (relative)
     * top of CodeIgniter.php. The main reason we use this is to permit
     * PHP errors to be logged in our own log files since the user may
     * not have access to server logs. Since this function effectively
     * intercepts PHP errors, however, we also need to display errors
     * based on the current error_reporting level.
     * We do that with the use of a PHP error template.
     *
     * @param       int         $severity
     * @param       string      $message
     * @param       string      $filepath
     * @param       int         $line
     * @return      void
     */
    function _error_handler($severity, $message, $filepath, $line)
    {
        // We don't bother with "strict" notices since they tend to fill up
        // the log file with excess information that isn't normally very helpful.
        // For example, if you are running PHP 5 and you use version 4 style
        // class functions (without prefixes like "public", "private", etc.)
        // you'll get notices telling you that these have been deprecated.
        if ($severity == E_STRICT)
        {
            return;
        }

        $is_error = (((E_ERROR | E_COMPILE_ERROR | E_CORE_ERROR | E_USER_ERROR) & $severity) === $severity);

        // When an error occurred, set the status header to '500 Internal Server Error'
        // to indicate to the client something went wrong.
        // This can't be done within the $_error->show_php_error method because
        // it is only called when the display_errors flag is set (which isn't usually
        // the case in a production environment) or when errors are ignored because
        // they are above the error_reporting threshold.
        if ($is_error)
        {
            set_status_header(500);
        }

        // Should we ignore the error? We'll get the current error_reporting
        // level and add its bits with the severity bits to find out.
        if (($severity & error_reporting()) !== $severity)
        {
            return;
        }

        $_error =& load_class('Exceptions', 'core');
        $_error->log_exception($severity, $message, $filepath, $line);

        // Should we display the error?
        if (str_ireplace(array('off', 'none', 'no', 'false', 'null'), '', ini_get('display_errors')))
        {
            $_error->show_php_error($severity, $message, $filepath, $line);
        }

        // If the error is fatal, the execution of the script should be stopped because
        // errors can't be recovered from. Halting the script conforms with PHP's
        // default error handling. See http://www.php.net/manual/en/errorfunc.constants.php
        if ($is_error)
        {
            exit(1); // EXIT_ERROR
        }
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('html_escape'))
{
    /**
     * Returns HTML escaped variable.
     *
     * @param       mixed   $var            The input string or array of strings to be escaped.
     * @param       bool    $double_encode  $double_encode set to FALSE prevents escaping twice.
     * @return      mixed                   The escaped string or array of strings as a result.
     */
    function html_escape($var, $double_encode = TRUE)
    {
        if (empty($var))
        {
            return $var;
        }

        $charset = config_item('charset');

        // Added by Ivan Tcholakov, 25-AUG-2015.
        // Ivan: For supporting PHP 5.2.0, unofficially.
        $is_php_5_2_3 = is_php('5.2.3');
        //

        if (is_array($var))
        {
            array_walk_recursive($var, '_html_escape_callback', array($charset, $double_encode, $is_php_5_2_3));
            return $var;
        }

        if ($is_php_5_2_3)
        {
            return htmlspecialchars($var, ENT_QUOTES, $charset, $double_encode);
        }

        return htmlspecialchars($var, ENT_QUOTES, $charset);
    }

    function _html_escape_callback(& $value, $key, $options)
    {
        if ($options[2])
        {
            $value = htmlspecialchars($value, ENT_QUOTES, $options[0], $options[1]);
        }
        else
        {
            $value = htmlspecialchars($value, ENT_QUOTES, $options[0]);
        }
    }
}

// ------------------------------------------------------------------------

if ( ! function_exists('_stringify_attributes'))
{
    /**
     * Stringify attributes for use in HTML tags.
     *
     * Helper function used to convert a string, array, or object
     * of attributes to a string.
     *
     * @param       mixed       string, array, object
     * @param       bool
     * @return      string
     */
    function _stringify_attributes($attributes, $js = FALSE)
    {
        $atts = NULL;

        if (empty($attributes))
        {
            return $atts;
        }

        // Added by Ivan Tcholakov, 03-JAN-2016.
        if (!$js)
        {
            return html_attr($attributes);
        }
        //

        if (is_string($attributes))
        {
            return ' '.$attributes;
        }

        $attributes = (array) $attributes;

        foreach ($attributes as $key => $val)
        {
            $atts .= ($js) ? $key.'='.$val.',' : ' '.$key.'="'.$val.'"';
        }

        return rtrim($atts, ',');
    }
}

// ------------------------------------------------------------------------

// Processing HTML Attributes
// Ivan Tcholakov, 2016.

if (!function_exists('html_attr')) {

    function html_attr($attributes) {

        $attr = new HTML_Attributes($attributes);

        return (string) $attr;
    }

}

if (!function_exists('html_attr_get')) {

    function html_attr_get($attributes, $name) {

        $attr = new HTML_Attributes($attributes);

        return $attr->getAttribute($name);
    }

}

if (!function_exists('html_attr_set')) {

    function html_attr_set($attributes, $name, $value = null) {

        $attr = new HTML_Attributes($attributes);

        return (string) $attr->setAttribute($name, $value);
    }

}

if (!function_exists('html_attr_merge')) {

    function html_attr_merge($attributes1, $attributes2) {

        $attr1 = new HTML_Attributes($attributes1);
        $attr2 = new HTML_Attributes($attributes2);

        $class2 = $attr2->getAttribute('class');
        $attr2->removeAttribute('class');
        $attr1->addClass($class2);

        return (string) $attr1->mergeAttributes((string) $attr2);
    }

}

if (!function_exists('html_attr_remove')) {

    function html_attr_remove($attributes, $name) {

        $attr = new HTML_Attributes($attributes);

        return (string) $attr->removeAttribute($name);
    }

}

if (!function_exists('html_attr_has_class')) {

    function html_attr_has_class($attributes, $class) {

        $attr = new HTML_Attributes($attributes);

        return $attr->hasClass($class);
    }

}

if (!function_exists('html_attr_add_class')) {

    function html_attr_add_class($attributes, $class) {

        $attr = new HTML_Attributes($attributes);

        return (string) $attr->addClass($class);
    }

}

if (!function_exists('html_attr_remove_class')) {

    function html_attr_remove_class($attributes, $class) {

        $attr = new HTML_Attributes($attributes);

        return (string) $attr->removeClass($class);
    }

}

// End Processing HTML Attributes

// ------------------------------------------------------------------------
