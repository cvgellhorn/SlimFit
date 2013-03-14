<?php

/**
 * Main data model
 *
 * @author cvgellhorn
 */
class App_Model implements ArrayAccess
{
	/**
	 * @var data to return
	 */
	protected $_data = array();
	
	/**
     * Setter/Getter underscore transformation cache
     *
     * @var array
     */
    protected static $_underscoreCache = array();
	
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
	public function getData()
	{
		return $this->_data;
	}
	
	/**
	 * Set data to $this object
	 * 
	 * @param array $data Data array
	 */
	public function setData($data)
	{
		$this->_data = $data;
	}
	
	/**
     * Attribute setter
     *
     * @param string $var
     * @param mixed $value
     */
	public function __set($key, $value)
	{
		$key = $this->_underscore($key);
		$this->_data[$key] = $value;
	}
	
	/**
     * Attribute getter
     *
     * @param string $var
     * @return mixed
     */
	public function __get($key)
	{
		$key = $this->_underscore($key);
		if(!isset($this->_data[$key])) {
			return null;
		}
		
		return $this->_data[$key];
	}
	
	/**
	 * Deprecated fallback method (magic getter/setter)
	 * 
	 * @param string $method Method name
	 * @param mixed $params Method params
	 * @return mixed
	 */
	public function __call($method, $params)
	{
		switch (substr($method, 0, 3)) {
			case 'get':
				$key = $this->_underscore(substr($method, 3));
				return isset(self::$_data[$key]) ? self::$_data[$key] : null;
			case 'set':
				$key = $this->_underscore(substr($method, 3));
				$this->_data[$key] = $params[0];
				return $this;
			case 'has' :
                $key = $this->_underscore(substr($method,3));
                return isset($this->_data[$key]);
			case 'uns':
				$key = $this->_underscore(substr($method, 5));
				unset($this->_data[$key]);
				break;
		}
	}
	
	/**
     * Converts field names for setters and geters
     *
     * $this->setMyField($value) === $this->setData('my_field', $value)
     * Uses cache to eliminate unneccessary preg_replace
     *
     * @param string $name
     * @return string
     */
    protected function _underscore($name)
    {
        if (isset(self::$_underscoreCache[$name])) {
            return self::$_underscoreCache[$name];
        }
		
        $result = strtolower(preg_replace('/(.)([A-Z])/', '$1_$2', $name));
        self::$_underscoreCache[$name] = $result;
        return $result;
    }
	
	/**
	 * Converts field names in getter setters format
	 *
	 * my_filed_name === MyFieldName
	 * @param string $name
	 * @param string $destSep Destination separator
	 * @param string $srcSep Source separator
	 * @return string
	 */
    protected function _camelize($name, $destSep = '', $srcSep = '_')
    {
		//[function] protected function _camelize($name) {
		// return uc_words($name, '');
		//[function] uc_words($str, $destSep='_', $srcSep='_')
		return str_replace(' ', $destSep, ucwords(str_replace($srcSep, ' ', $str)));
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