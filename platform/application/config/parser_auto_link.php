<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author Ivan Tcholakov <ivantcholakov@gmail.com>, 2013
 * @license The MIT License, http://opensource.org/licenses/MIT
 */

// What kind of links to be parsed: 'email', 'url', or 'both'
$config['type'] = 'both';

// Atthibutes to be added to automatically created links.
// Example: $config['attributes'] = 'target="_blank"';
$config['attributes'] = '';

// This option enforces template full paths to be given for method parse().
$config['full_path'] = FALSE;
