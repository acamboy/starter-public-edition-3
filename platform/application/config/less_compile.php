<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author Ivan Tcholakov <ivantcholakov@gmail.com>, 2013
 * @license The MIT License, http://opensource.org/licenses/MIT
 */

/**
 * How to recompile these LESS-sources:
 * make sure the destination folders and files are writable.
 * open a terminal at the folder platform/www/ and write the following command:
 * php cli.php less compile
 */

$config['less_compile'] = array(

    array(
        'source' => FCPATH.'assets/less/lib/bootstrap-3/bootstrap.less',
        'destination' => FCPATH.'assets/css/lib/bootstrap-3/bootstrap.css',
        'compress' => false
    ),
    array(
        'source' => FCPATH.'assets/less/lib/bootstrap-3/bootstrap.less',
        'destination' => FCPATH.'assets/css/lib/bootstrap-3/bootstrap.min.css',
        'compress' => true
    ),

    array(
        'source' => FCPATH.'assets/less/lib/bootstrap-3/theme.less',
        'destination' => FCPATH.'assets/css/lib/bootstrap-3/bootstrap-theme.css',
        'compress' => false
    ),
    array(
        'source' => FCPATH.'assets/less/lib/bootstrap-3/theme.less',
        'destination' => FCPATH.'assets/css/lib/bootstrap-3/bootstrap-theme.min.css',
        'compress' => true
    ),

    array(
        'source' => FCPATH.'assets/less/lib/font-awesome-4/font-awesome.less',
        'destination' => FCPATH.'assets/css/lib/font-awesome-4/font-awesome.css',
        'compress' => false
    ),
    array(
        'source' => FCPATH.'assets/less/lib/font-awesome-4/font-awesome.less',
        'destination' => FCPATH.'assets/css/lib/font-awesome-4/font-awesome.min.css',
        'compress' => true
    ),

);
