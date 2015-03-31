<?php defined('BASEPATH') OR exit('No direct script access allowed.');

/**
 * @author Ivan Tcholakov <ivantcholakov@gmail.com>, 2014-2015
 * @license The MIT License, http://opensource.org/licenses/MIT
 */

class Settings {

    public $encryption;

    protected $settings;

    protected $ci;
    protected $settings_model;

    public function __construct() {

        $this->ci = get_instance();

        $this->ci->load->model('settings_model');
        $this->settings_model = $this->ci->settings_model;

        $this->ci->load->helper('settings');

        $this->ci->load->library('encryption', null, 'settings_encryption');
        $this->encryption = $this->ci->settings_encryption;

        $encryption_key = $this->ci->config->item('encryption_key_for_settings');
        $this->encryption->initialize(array('cipher' => 'aes-128', 'mode' => 'cbc', 'key' => $encryption_key));

        $this->refresh();
    }

    // Reads all the setting from database only.
    public function get_all() {

        if (!$this->settings_model->table_exists()) {
            return false;
        }

        return $this->settings;
    }

    // Reads a setting from the database.
    // If the settings is not found, then a setting from the configuration
    // files under the same name will be tried to be returned.
    public function get($key) {

        if (is_array($key)) {

            $result = array();

            foreach ($key as $k) {
                $result[$k] = $this->get($k);
            }

            return $result;
        }

        $key = (string) $key;

        if ($key == '') {
            return null;
        }

        if (array_key_exists($key, $this->settings)) {
            return $this->settings[$key];
        }

        return $this->ci->config->item($key);
    }

    // Sets a database stored setting.
    // Database table should be created in order to use this method.
    // See Settings_model class for information about table structure.
    public function set($key, $value = null, $encrypt = false) {

        if (is_array($key)) {

            foreach ($key as $k => $v) {
                $this->set($k, $v, $encrypt);
            }

            return $this;
        }

        $key = (string) $key;

        if ($key == '') {
            return $this;
        }

        if ($encrypt) {

            $this->settings_model->delete_many_by('name', $key);
            $key = $key.'__encrypted';
            $value = $this->encryption->encrypt($value);

        } else {

            $this->settings_model->delete_many_by('name', $key.'__encrypted');
        }

        $id = $this->settings_model->select('id')->where('name', $key)->as_value()->first();
        $data = array('name' => $key, 'value' => $value);

        if ($id === null) {
            $this->settings_model->insert($data);
        } else {
            $this->settings_model->update((int) $id, $data);
        }

        return $this;
    }

    // Reads all the settings from database and holds them within memory.
    public function refresh() {

        $this->settings = array();

        if (!$this->settings_model->table_exists()) {
            return $this;
        }

        $data = ci()->settings_model
            ->select('name, value')
            ->order_by('id')
            ->find();

        if (!empty($data)) {

            foreach ($data as $item) {

                $name = (string) $item['name'];
                $value = $item['value'];

                // Just in case, skip missing keys.
                if ($name == '') {
                    continue;
                }

                if (preg_match('/__encrypted$/', $name)) {

                    $original_name = preg_replace('/__encrypted$/', '', $name);

                    if ($original_name == '') {
                        continue;
                    }

                    if (array_key_exists($original_name, $this->settings)) {
                        unset($this->settings[$original_name]);
                    }

                    $name = $original_name;
                    $value = $this->encryption->decrypt($value);
                }

                // Just in case, skip duplicate keys.
                if (array_key_exists($name, $this->settings)) {
                    continue;
                }

                if (is_string($value) && is_numeric($value)) {

                    if (ctype_digit($value)) {

                        if (strlen($value) == 1 || strpos($value, '0') !== 0) {
                            $this->settings[$name] = (int) $value;
                        } else {
                            $this->settings[$name] = $value;
                        }

                    } else {

                        $this->settings[$name] = (double) $value;
                    }

                } else {

                    $this->settings[$name] = $value;
                }
            }
        }

        return $this;
    }

}
