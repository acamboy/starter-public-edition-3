<?php defined('BASEPATH') OR exit('No direct script access allowed.');

$config['basePath'] = BASE_URL.'assets/js/ckeditor/';
$config['config']['baseHref'] = BASE_URL;
$config['config']['fullPage'] = false;
$config['config']['language'] = 'en';
$config['config']['defaultLanguage'] = 'en';
$config['config']['contentsLanguage'] = 'en';
$config['config']['contentsLangDirection'] = 'ltr';

$config['config']['contentsCss'] = array();
$config['config']['contentsCss'][] = BASE_URL.'assets/css/lib/bootstrap-3/bootstrap.min.css';
$config['config']['contentsCss'][] = BASE_URL.'assets/css/lib/font-awesome-4/font-awesome.min.css';
$config['config']['contentsCss'][] = BASE_URL.'assets/css/lib/editor.css';

$config['config']['width'] = '';
$config['config']['height'] = '100';
$config['config']['resize_enabled'] = false;
$config['textareaAttributes'] = array('rows' => 8, 'cols' => 60);

$config['config']['entities_latin'] = false;
$config['config']['entities_greek'] = false;

$config['config']['forcePasteAsPlainText'] = true;
$config['config']['toolbarCanCollapse'] = false;

$config['config']['allowedContent'] = true;
