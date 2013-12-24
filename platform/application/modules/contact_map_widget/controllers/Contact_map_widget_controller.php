<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author Ivan Tcholakov <ivantcholakov@gmail.com>, 2013
 * @license The MIT License, http://opensource.org/licenses/MIT
 *
 * Put this widget within a view in the following way:
 * echo Modules::run('contact_map_widget/index');
 */

class Contact_map_widget_controller extends MY_Controller {

    public function __construct() {

        parent::__construct();

        $this->load
            ->library('settings')
            ->helper('language')
            ->helper('url')
            ->helper('asset')
            ->parser('i18n')
            ->language('contact')
        ;
    }

    public function _remap() {

        show_404();
    }

    public function index() {

        $data = $this->settings->get(array(
            'contact_map',
        ));

        // TODO: A workaround, 24-DEC-2013.
        //echo $this->i18n->parse('contact_map_widget', $data, TRUE);
        echo $this->i18n->parse_string($this->load->view('contact_map_widget', $data, true), null, true);
        //
    }

}
