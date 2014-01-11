<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author Ivan Tcholakov <ivantcholakov@gmail.com>, 2013
 * @license The MIT License, http://opensource.org/licenses/MIT
 */

class Readme_controller extends Base_Controller {

    public function __construct() {

        parent::__construct();

        $this->load->parser();

        $this->template
            ->title('README')
        ;
    }

    public function index() {

        $path = '';
        $content = '# The file README.md has not been found.';

        if (file_exists(PLATFORMPATH.'../README.md')) {
            $path = realpath(PLATFORMPATH.'../README.md');
        }
        elseif (file_exists(FCPATH.'../README.md')) {
            $path = realpath(FCPATH.'../README.md');
        }
        elseif (file_exists(FCPATH.'README.md')) {
            $path = FCPATH.'README.md';
        }

        $this->template
            ->set(compact('path', 'content'))
            ->build('readme');
    }

}
