<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author Ivan Tcholakov <ivantcholakov@gmail.com>, 2016
 * @license The MIT License, http://opensource.org/licenses/MIT
 */

// Twig's 'debug' option.
$config['debug'] = ENVIRONMENT !== 'production';

// Character set used by the Twig template engine.
// NULL means config_item('charset'), i.e. the character set of the site.
$config['charset'] = null;

// Caching: An absolute path where to store the compiled templates,
// or false to disable caching (which is the default).
// Disable caching for now.
//$config['cache'] = TWIG_CACHE;
$config['cache'] = false;

// The default timezone to be used by Twig.
$config['timezone'] = date_default_timezone_get();

// Extending the Twig parser: Load CodeIgniter helpers
// that serve implemented for Twig functions and filters.
$config['helpers'] = array(
    'text',
);

// Extending the Twig parser: Choose Twig extensions to be loaded.
$config['extensions'] = array(
    'Twig_Extension_StringLoader',
    array('Twig_Extension_Debug', ENVIRONMENT !== 'production'),
    array('Twig_Extensions_Extension_Text', false), // TRUE enables the corresponding extension.
    array('Twig_Extensions_Extension_I18n', false),
    array('Twig_Extensions_Extension_Intl', false),
    array('Twig_Extensions_Extension_Array', false),
    array('Twig_Extensions_Extension_Date', false),
);

// Extending the Twig parser: Extra-functions.
$config['functions'] = array(
    // Debugging Previews
    array('print_d', 'print_d', array('is_safe' => array('html')), ENVIRONMENT !== 'production'),
    array('print_r', array('Parser_Twig_Extension_Debug', 'print_r'), array('is_safe' => array('html')), ENVIRONMENT !== 'production'),
    array('var_export', array('Parser_Twig_Extension_Debug', 'var_export'), array('is_safe' => array('html')), ENVIRONMENT !== 'production'),
    // CodeIgniter's Helpers
    array('lang', array('Parser_Twig_Extension_Language', 'lang')),
    // Configuration, Settings
    array('config', array('Parser_Twig_Extension_Setting', 'config')),
    array('setting', array('Parser_Twig_Extension_Setting', 'setting')),
);

// Extending the Twig parser: Extra-filters.
$config['filters'] = array(
    // Type Casting
    array('boolean', array('Parser_Twig_Extension_TypeCasting', 'boolean'), array('is_safe' => array('html'))),
    array('bool', array('Parser_Twig_Extension_TypeCasting', 'boolean'), array('is_safe' => array('html'))),
    array('integer', array('Parser_Twig_Extension_TypeCasting', 'integer'), array('is_safe' => array('html'))),
    array('int', array('Parser_Twig_Extension_TypeCasting', 'integer'), array('is_safe' => array('html'))),
    array('float', array('Parser_Twig_Extension_TypeCasting', 'float'), array('is_safe' => array('html'))),
    array('double', array('Parser_Twig_Extension_TypeCasting', 'float'), array('is_safe' => array('html'))),
    array('real', array('Parser_Twig_Extension_TypeCasting', 'float'), array('is_safe' => array('html'))),
    array('string', array('Parser_Twig_Extension_TypeCasting', 'string')),
    array('array', array('Parser_Twig_Extension_TypeCasting', 'twig_array')),
    array('object', array('Parser_Twig_Extension_TypeCasting', 'object')),
    array('null', array('Parser_Twig_Extension_TypeCasting', 'null'), array('is_safe' => array('html'))),
    // PHP Functions
    array('count', 'count', array('is_safe' => array('html'))),
    array('gettype', 'gettype', array('is_safe' => array('html'))),
    array('money_format', array('Parser_Twig_Extension_Php', 'money_format')),
    // CodeIgniter's Helpers
    'character_limiter',
    array('xss_clean', array('Parser_Twig_Extension_Html', 'xss_clean'), array('is_safe' => array('html'))),
);

// Extending the Twig parser: Extra-tests (is * operators).
$config['tests'] = array(
    array('array', 'is_array'),
    array('bool', 'is_bool'),
    array('boolean', 'is_bool'),
    array('float', 'is_float'),
    array('int', 'is_int'),
    array('integer', 'is_integer'),
    array('numeric', 'is_numeric'),
    array('object', 'is_object'),
    array('scalar', 'is_scalar'),
    array('string', 'is_string'),
);
