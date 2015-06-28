<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Keys Controller
 * This is a basic Key Management REST controller to make and delete keys
 *
 * @package         CodeIgniter
 * @subpackage      Rest Server
 * @category        Controller
 * @author          Phil Sturgeon, Chris Kacerguis
 * @license         MIT
 * @link            https://github.com/chriskacerguis/codeigniter-restserver
 */
class Restserver_api_key_controller extends REST_Controller {

    protected $methods = array(
            'index_put' => array('level' => 10, 'limit' => 10),
            'index_delete' => array('level' => 10),
            'level_post' => array('level' => 10),
            'regenerate_post' => array('level' => 10),
        );

    /**
     * Key Create
     * Insert a key into the database
     *
     * @access    public
     * @return    void
     */
    public function index_put()
    {
        // Build a new key
        $key = self::_generate_key();

        // If no key level provided, provide a generic key
        $level = $this->put('level') ? $this->put('level') : 1;
        $ignore_limits = ctype_digit($this->put('ignore_limits')) ? (int) $this->put('ignore_limits') : 1;

        // Insert the new key
        if (self::_insert_key($key, array('level' => $level, 'ignore_limits' => $ignore_limits)))
        {
            $this->response(array(
                'status' => 1,
                'key' => $key
            ), 201); // 201 = Created
        }
        else
        {
            $this->response(array(
                'status' => 0,
                'error' => 'Could not save the key'
            ), 500); // 500 = Internal Server Error
        }
    }

    // --------------------------------------------------------------------

    /**
     * Key Delete
     * Remove a key from the database to stop it working
     *
     * @access    public
     * @return    void
     */
    public function index_delete()
    {
        $key = $this->delete('key');

        // Does this key exist?
        if (!self::_key_exists($key))
        {
            // It doesn't appear the key exists
            $this->response(array(
                'error' => 'Invalid API Key'
            ), 400); // 400 = Bad Request
        }

        // Destroy it
        self::_delete_key($key);

        // Respond that the key was destroyed
        $this->response(array(
            'status' => 1,
            'success' => 'API Key was deleted.'
            ), 204); // 204 = Success, No Content
    }

    // --------------------------------------------------------------------

    /**
     * Update Key
     * Change the level
     *
     * @access    public
     * @return    void
     */
    public function level_post()
    {
        $key = $this->post('key');
        $new_level = $this->post('level');

        // Does this key exist?
        if (!self::_key_exists($key))
        {
            // It doesn't appear the key exists
            $this->response(array(
                'error' => 'Invalid API Key'
            ), 400); // 400 = Bad Request
        }

        // Update the key level
        if (self::_update_key($key, array('level' => $new_level)))
        {
            $this->response(array(
                'status' => 1,
                'success' => 'API Key was updated'
            ), 200); // 200 = OK
        }
        else
        {
            $this->response(array(
                'status' => 0,
                'error' => 'Could not update the key level'
            ), 500); // 500 = Internal Server Error
        }
    }

    // --------------------------------------------------------------------

    /**
     * Update Key
     * Change the level
     *
     * @access    public
     * @return    void
     */
    public function suspend_post()
    {
        $key = $this->post('key');

        // Does this key exist?
        if (!self::_key_exists($key))
        {
            // It doesn't appear the key exists
            $this->response(array(
                'error' => 'Invalid API Key'
            ), 400); // 400 = Bad Request
        }

        // Update the key level
        if (self::_update_key($key, array('level' => 0)))
        {
            $this->response(array(
                'status' => 1,
                'success' => 'Key was suspended'
            ), 200); // 200 = OK
        }
        else
        {
            $this->response(array(
                'status' => 0,
                'error' => 'Could not suspend the user'
            ), 500); // 500 = Internal Server Error
        }
    }

    // --------------------------------------------------------------------

    /**
     * Regenerate Key
     * Remove a key from the database to stop it working
     *
     * @access    public
     * @return    void
     */
    public function regenerate_post()
    {
        $old_key = $this->post('key');
        $key_details = self::_get_key($old_key);

        // Does this key exist?
        if (!$key_details)
        {
            // It doesn't appear the key exists
            $this->response(array(
                'error' => 'Invalid API Key'
            ), 400); // 400 = Bad Request
        }

        // Build a new key
        $new_key = self::_generate_key();

        // Insert the new key
        if (self::_insert_key($new_key, array('level' => $key_details->level, 'ignore_limits' => $key_details->ignore_limits)))
        {
            // Suspend old key
            self::_update_key($old_key, array('level' => 0));

            $this->response(array(
                'status' => 1,
                'key' => $new_key
            ), 201); // 201 = Created
        }
        else
        {
            $this->response(array(
                'status' => 0,
                'error' => 'Could not save the key'
            ), 500); // 500 = Internal Server Error
        }
    }

    // --------------------------------------------------------------------

    /* Helper Methods */

    private function _generate_key()
    {
        do
        {
            // Generate a random salt
            // Modified by Ivan Tcholakov, 28-JUN-2015.
            //$salt = mcrypt_create_iv(16, MCRYPT_DEV_URANDOM);
            $salt = secure_random_bytes(16);
            //
            $new_key = substr($salt, 0, config_item('rest_key_length'));
        }
        while (self::_key_exists($new_key));
        // Already in the DB? Fail. Try again

        return $new_key;
    }

    // --------------------------------------------------------------------

    /* Private Data Methods */

    private function _get_key($key)
    {
        return $this->db->where('key', $key)->get(config_item('rest_keys_table'))->row();
    }

    // --------------------------------------------------------------------

    private function _key_exists($key)
    {
        return $this->db->where('key', $key)->count_all_results(config_item('rest_keys_table')) > 0;
    }

    // --------------------------------------------------------------------

    private function _insert_key($key, $data)
    {

        $data['key'] = $key;
        $data['date_created'] = function_exists('now') ? now() : time();

        return $this->db->set($data)->insert(config_item('rest_keys_table'));
    }

    // --------------------------------------------------------------------

    private function _update_key($key, $data)
    {
        return $this->db->where('key', $key)->update(config_item('rest_keys_table'), $data);
    }

    // --------------------------------------------------------------------

    private function _delete_key($key)
    {
        return $this->db->where('key', $key)->delete(config_item('rest_keys_table'));
    }

}
