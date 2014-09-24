<?php namespace SlimFit;

use ArrayAccess;

/**
 * Main data model
 *
 * @author cvgellhorn
 */
class Model implements ArrayAccess
{
	/**
	 * @var data to return
	 */
	protected $_data = array();
	
	/**
	 * App_Model cunstructor
	 *
	 * @param array Data array
	 */
	public function __cunstruct($data = array())
	{
		$this->_data = $data;
	}
	
	/**
	 * Get data from $this object
	 */
	public function getData($key = null)
	{
		if(null !== $key) {
			return (isset($this->_data[$key])) ? $this->_data[$key] : null;
		} else {
			return $this->_data;
		}
	}
	
	/**
	 * Set data to $this object
	 * 
	 * @param mixed $data Array data
	 * @param mixed $value Array data
	 */
	public function setData($data, $value = null)
	{
		if(is_array($data)) {
			$this->_data = $data;
		} else {
			$this->_data[$data] = $value;
		}
	}
	
	/**
	 * Count data elements
	 *
	 * @return int Elements count
	 */
	public function count()
	{
		return count($this->_data);
	}
	
	/**
     * Implementation of ArrayAccess::offsetSet()
     *
     * @link http://www.php.net/manual/en/arrayaccess.offsetset.php
     * @param string $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->_data[$offset] = $value;
    }

    /**
     * Implementation of ArrayAccess::offsetExists()
     *
     * @link http://www.php.net/manual/en/arrayaccess.offsetexists.php
     * @param string $offset
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return isset($this->_data[$offset]);
    }

    /**
     * Implementation of ArrayAccess::offsetUnset()
     *
     * @link http://www.php.net/manual/en/arrayaccess.offsetunset.php
     * @param string $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->_data[$offset]);
    }

    /**
     * Implementation of ArrayAccess::offsetGet()
     *
     * @link http://www.php.net/manual/en/arrayaccess.offsetget.php
     * @param string $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return isset($this->_data[$offset]) ? $this->_data[$offset] : null;
    }
}