<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Format class
 * Help convert between various formats such as XML, JSON, CSV, etc.
 *
 * @author    Phil Sturgeon
 * @license   http://philsturgeon.co.uk/code/dbad-license
 */
class Format {

    protected $_data = array();   // Array to convert
    protected $_from_type = NULL; // View filename

    /**
     * DO NOT CALL THIS DIRECTLY, USE factory()
     *
     * @param null $data
     * @param null $from_type
     *
     * @throws Exception
     */
    public function __construct($data = NULL, $from_type = NULL)
    {
        get_instance()->load->helper('inflector');

        // If the provided data is already formatted we should probably convert it to an array
        if ($from_type !== NULL)
        {
            if (method_exists($this, '_from_' . $from_type))
            {
                $data = call_user_func(array($this, '_from_' . $from_type), $data);
            }
            else
            {
                throw new Exception('Format class does not support conversion from "' . $from_type . '".');
            }
        }

        $this->_data = $data;
    }

    /**
     * Returns an instance of the Format class
     * e.g: echo $this->format->factory(array('foo' => 'bar'))->to_xml();
     *
     * @param $data
     * @param null $from_type
     *
     * @return mixed
     */
    public function factory($data, $from_type = NULL)
    {
        // Stupid stuff to emulate the "new static()" stuff in this libraries PHP 5.3 equivalent
        $class = __CLASS__;

        return new $class($data, $from_type);
    }

    // FORMATING OUTPUT ---------------------------------------------------------

    /**
     * to_array
     *
     * @param null $data
     *
     * @return array
     */
    public function to_array($data = NULL)
    {
        // As the return value should be a string, it makes no sense
        // to return an array datatype as that will result in an error or sorts
        return $this->to_json($data);
    }

    /**
     * Format XML for output
     *
     * @param null $data
     * @param null $structure
     * @param string $basenode
     *
     * @return mixed
     */
    public function to_xml($data = NULL, $structure = NULL, $basenode = 'xml')
    {
        if ($data === NULL && !func_num_args())
        {
            $data = $this->_data;
        }

        // turn off compatibility mode as simple xml throws a wobbly if you don't.
        if (ini_get('zend.ze1_compatibility_mode') == 1)
        {
            ini_set('zend.ze1_compatibility_mode', 0);
        }

        if ($structure === NULL)
        {
            $structure = simplexml_load_string("<?xml version='1.0' encoding='utf-8'?><$basenode />");
        }

        // Force it to be something useful
        if (!is_array($data) && !is_object($data))
        {
            $data = (array) $data;
        }

        foreach ($data as $key => $value)
        {

            //change false/true to 0/1
            if (is_bool($value))
            {
                $value = (int) $value;
            }

            // no numeric keys in our xml please!
            if (is_numeric($key))
            {
                // make string key...
                $key = (singular($basenode) != $basenode) ? singular($basenode) : 'item';
            }

            // replace anything not alpha numeric
            $key = preg_replace('/[^a-z_\-0-9]/i', '', $key);

            if ($key === '_attributes' && (is_array($value) || is_object($value)))
            {
                $attributes = $value;
                if (is_object($attributes))
                {
                    $attributes = get_object_vars($attributes);
                }

                foreach ($attributes as $attributeName => $attributeValue)
                {
                    $structure->addAttribute($attributeName, $attributeValue);
                }
            }
            // if there is another array found recursively call this function
            elseif (is_array($value) || is_object($value))
            {
                $node = $structure->addChild($key);

                // recursive call.
                $this->to_xml($value, $node, $key);
            }
            else
            {
                // add single node.
                $value = htmlspecialchars(html_entity_decode($value, ENT_QUOTES, 'UTF-8'), ENT_QUOTES, "UTF-8");

                $structure->addChild($key, $value);
            }
        }

        return $structure->asXML();
    }

    /**
     * Format HTML for output
     *
     * @return mixed
     */
    public function to_html()
    {
        $data = (array) $this->_data;

        // Multi-dimensional array
        if (isset($data[0]) && is_array($data[0]))
        {
            $headings = array_keys($data[0]);
        }

        // Single array
        else
        {
            $headings = array_keys($data);
            $data = array($data);
        }

        $ci = get_instance();
        $ci->load->library('table');

        $ci->table->set_heading($headings);

        foreach ($data as &$row)
        {
            $ci->table->add_row($row);
        }

        return $ci->table->generate();
    }

    /**
     * Format CSV for output
     *
     * @return mixed
     */
    public function to_csv()
    {
        $data = (array) $this->_data;

        // Multi-dimensional array
        if (isset($data[0]) && is_array($data[0]))
        {
            $headings = array_keys($data[0]);
        }

        // Single array
        else
        {
            $headings = array_keys($data);
            $data = array($data);
        }

        $output = '"' . implode('","', $headings) . '"' . PHP_EOL;
        foreach ($data as &$row)
        {
            // Modified by Ivan Tcholakov, 01-JUN-2014.
            //$row = str_replace('"', '""', $row); // Escape dbl quotes per RFC 4180
            //$output .= '"' . implode('","', $row) . '"' . PHP_EOL;
            @ $output .= '"' . implode('","', str_replace('"', '""', $row)) . '"' . PHP_EOL;
            //
        }

        return $output;
    }

    /**
     * Encode as JSON
     *
     * @return mixed
     */
    public function to_json()
    {
        // Modified by Ivan Tcholakov, 28-JUN-2015.
        //$callback = $this->input->get('callback');
        $callback = get_instance()->input->get('callback');
        //
        if (empty($callback) === TRUE)
        {
            // Modified by Ivan Tcholakov, 28-JUN-2015.
            //return json_encode($this->_data, JSON_PRETTY_PRINT);
            return is_php('5.4') ? json_encode($this->_data, JSON_PRETTY_PRINT) : json_encode($this->_data);
            //
        }

        // We only honour jsonp callback which are valid javascript identifiers
        elseif (preg_match('/^[a-z_\$][a-z0-9\$_]*(\.[a-z_\$][a-z0-9\$_]*)*$/i', $callback))
        {
            // Set the content type
            header("Content-Type: application/javascript");

            // Return the data as encoded json with a callback
            return $callback . '(' . json_encode($this->_data) . ');';
        }

        // An invalid jsonp callback function provided.
        // Though I don't believe this should be hardcoded here
        $this->_data['warning'] = 'INVALID JSONP CALLBACK: ' . $callback;

        return json_encode($this->_data);
    }

    /**
     * Encode as a serialized array
     */
    public function to_serialized()
    {
        return serialize($this->_data);
    }

    /**
     * Output as a string representing the PHP structure
     */
    public function to_php()
    {
        return var_export($this->_data, TRUE);
    }

    /**
     * @param $string
     *
     * @return array
     */
    protected function _from_xml($string)
    {
        return $string ? (array) simplexml_load_string($string, 'SimpleXMLElement', LIBXML_NOCDATA) : array();
    }

    /**
     * @param $string
     *
     * @return array
     */
    protected function _from_csv($string)
    {
        $data = array();

        // Splits
        $rows = explode("\n", trim($string));
        $headings = explode(',', array_shift($rows));
        foreach ($rows as $row)
        {
            // The substr removes " from start and end
            $data_fields = explode('","', trim(substr($row, 1, -1)));

            if (count($data_fields) == count($headings))
            {
                $data[] = array_combine($headings, $data_fields);
            }
        }

        return $data;
    }

    /**
     * @param $string
     *
     * @return mixed
     */
    private function _from_json($string)
    {
        return json_decode(trim($string));
    }

    /**
     * @param $string
     *
     * @return mixed
     */
    private function _from_serialize($string)
    {
        return unserialize(trim($string));
    }


    /**
     * @param $string
     *
     * @return string
     */
    private function _from_php($string)
    {
        return trim($string);
    }

}
