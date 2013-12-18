<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author Ivan Tcholakov <ivantcholakov@gmail.com>, 2013
 * @license The MIT License, http://opensource.org/licenses/MIT
 */

class Welcome extends Base_Controller {

    public function __construct() {

        parent::__construct();

        $this->template
            ->title('Application Starter 3 Public Edition')
        ;
    }

    public function index() {

        // This is just a demo page, code is done in ad-hoc manner.

        $this->load->library('kcaptcha', null, 'captcha');

        // Collecting diagnostics data.

        $writable_folders = array(

            'platform/writable/' =>
                array(
                    'path' => WRITABLEPATH,
                    'is_writable' => NULL
                ),
        );

        foreach ($writable_folders as $key => $folder) {

            $writable_folders[$key]['is_writable'] = is_really_writable($folder['path']);
        }

        $mailer_enabled = (bool) $this->settings->get('mailer_enabled');

        // Diagnostics data decoration.

        $diagnostics = array();

        $diagnostics[] = '<strong>Writable folders check:</strong>';

        foreach ($writable_folders as $key => $folder) {
            
            if ($writable_folders[$key]['is_writable']) {

                $diagnostics[] = "$key - <span style=\"color: green\">writable</span>";

            } else {

                $diagnostics[] = "$key - <span style=\"color: red\">make it writable</span>";
            }
        }

        $diagnostics[] = '<strong>Mailer:</strong>';

        if ($mailer_enabled) {

            $diagnostics[] = 'Mailer service - <span style="color: green">enabled</span>';

        } else {

            $diagnostics[] = 'Mailer service - <span style="color: red">disabled. Check $config[\'mailer_enabled\'] option within platform/application/config/config_site.php. Check also the mailer settings within platform/application/config/email.php.</span>';
        }

        $diagnostics = implode('<br />', $diagnostics);

        $this->template
            ->set('diagnostics', $diagnostics)
            ->set_partial('scripts', 'welcome_scripts')
            ->build('welcome_message');
    }

    public function test_captcha() {

        if (!IS_AJAX_REQUEST) {
            show_404();
        }

        $this->load->library('kcaptcha', null, 'captcha');

        $valid_user_input = $this->captcha->get_keystring();
        $invalid_user_input = $this->captcha->generate_keystring();

        ob_start();
?>

                Test user input <?php echo $valid_user_input; ?> : <strong><?php echo $this->captcha->valid($valid_user_input) ? 'valid' : 'invalid'; ?></strong>
                <br />
                Test user input <?php echo $invalid_user_input; ?> : <strong><?php echo $this->captcha->valid($invalid_user_input) ? 'valid' : 'invalid'; ?></strong>

<?php
        $output = ob_get_contents();
        ob_end_clean();

        $this->output->set_header('Content-type: text/html; charset=utf-8');
        $this->output->set_output($output);
    }

}
