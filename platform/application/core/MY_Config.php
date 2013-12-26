<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author Ivan Tcholakov <ivantcholakov@gmail.com>, 2013
 * @license The MIT License, http://opensource.org/licenses/MIT for my modifications.
 */

/* load the MX_Loader class */
if (!class_exists('MX_Config', false)) {
    require APPPATH.'third_party/MX/Config.php';
}

class MY_Config extends MX_Config {

    /**
     * Class constructor
     *
     * Sets the $config data from the primary config.php file as a class variable.
     *
     * @return    void
     */
    public function __construct()
    {
        $this->config =& get_config();
        log_message('debug', 'Config Class Initialized');

        global $DETECT_URL;

        // Set the base_url automatically if none was provided
        if (empty($this->config['base_url']))
        {
            $this->set_item('base_url', $DETECT_URL['base_url']);
        }

        if (!defined('BASE_URL')) {
            define('BASE_URL', $this->add_slash($this->base_url()));
        }

        if (!defined('BASE_URI')) {
            define('BASE_URI', $DETECT_URL['base_uri']);
        }

        if (!defined('SERVER_URL')) {
            define('SERVER_URL', $this->add_slash(str_replace(BASE_URI, '', BASE_URL)));
        }

        if (!defined('SITE_URL')) {
            define('SITE_URL', $this->add_slash($this->site_url()));
        }

        if (!defined('SITE_URI')) {
            define('SITE_URI', '/'.str_replace(SERVER_URL, '', SITE_URL));
        }

        if (!defined('CURRENT_URI')) {
            define('CURRENT_URI', $DETECT_URL['current_uri']);
        }

        if (!defined('CURRENT_URL')) {
            define('CURRENT_URL', rtrim(SERVER_URL, '/').CURRENT_URI);
        }

        // Added by Ivan Tcholakov, 26-DEC-2013.
        // See https://github.com/EllisLab/CodeIgniter/issues/2792
        if (!defined('IS_UTF8_CHARSET')) {
            define('IS_UTF8_CHARSET', strtolower($this->config['charset']) === 'utf-8');
        }
        //
    }

    // --------------------------------------------------------------------

    /**
     * Base URL
     *
     * Returns base_url [. uri_string]
     *
     * @uses        CI_Config::_uri_string()
     *
     * @param       string|string[]    $uri    URI string or an array of segments
     * @param       string    $protocol
     * @return      string
     */
    public function base_url($uri = '', $protocol = NULL)
    {
        // Added by Ivan Tcholakov, 09-NOV-2013.
        if (is_array($uri)) {
            $uri = implode('/', $uri);
        }
        //

        $base_url = $this->slash_item('base_url');

        if (isset($protocol))
        {
            $base_url = $protocol.substr($base_url, strpos($base_url, '://'));
        }

        return $base_url.ltrim($this->_uri_string($uri), '/');
    }

    // --------------------------------------------------------------------

    /**
     * Site URL
     *
     * Returns base_url . index_page [. uri_string]
     *
     * @access    public
     * @param     mixed    the URI string
     * @return    string
     */
    public function site_url($uri = '')
    {
        // Added by Ivan Tcholakov, 12-OCT-2013.
        if (is_array($uri)) {
            $uri = implode('/', $uri);
        }
        //

        if (empty($uri))
        {
            return $this->slash_item('base_url').$this->item('index_page');
        }

        $uri = $this->_uri_string($uri);

        if ($this->item('enable_query_strings') === FALSE)
        {
            $suffix = isset($this->config['url_suffix']) ? $this->config['url_suffix'] : '';

            if ($suffix !== '')
            {
                if (($offset = strpos($uri, '?')) !== FALSE)
                {
                    $uri = substr($uri, 0, $offset).$suffix.substr($uri, $offset);
                }
                else
                {
                    $uri .= $suffix;
                }
            }

            return $this->slash_item('base_url').$this->slash_item('index_page').$uri;
        }
        elseif (strpos($uri, '?') === FALSE)
        {
            $uri = '?'.$uri;
        }

        return $this->slash_item('base_url').$this->item('index_page').$uri;
    }

    // --------------------------------------------------------------------

    // Added by Ivan Tcholakov, 12-OCT-2013.
    protected function add_slash($string) {

        $string = (string) $string;

        if ($string != '') {
            $string = rtrim($string, '/').'/';
        }

        return $string;
    }

    // --------------------------------------------------------------------

    // Added by Ivan Tcholakov, 09-NOV-2013.
    public function base_uri($uri = '') {

        if (is_array($uri)) {
            $uri = implode('/', $uri);
        }

        return BASE_URI.ltrim($this->_uri_string($uri), '/');
    }

    // Added by Ivan Tcholakov, 09-NOV-2013.
    public function site_uri($uri = '') {

        if (is_array($uri)) {
            $uri = implode('/', $uri);
        }

        if (empty($uri))
        {
            return SITE_URI;
        }

        $uri = $this->_uri_string($uri);

        if ($this->item('enable_query_strings') === FALSE)
        {
            $suffix = isset($this->config['url_suffix']) ? $this->config['url_suffix'] : '';

            if ($suffix !== '')
            {
                if (($offset = strpos($uri, '?')) !== FALSE)
                {
                    $uri = substr($uri, 0, $offset).$suffix.substr($uri, $offset);
                }
                else
                {
                    $uri .= $suffix;
                }
            }

            return BASE_URI.$this->slash_item('index_page').$uri;
        }
        elseif (strpos($uri, '?') === FALSE)
        {
            $uri = '?'.$uri;
        }

        return BASE_URI.$this->item('index_page').$uri;
    }

}
