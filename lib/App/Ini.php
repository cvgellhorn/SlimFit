<?php

/**
 * App Initializer
 * 
 * @author cvgellhorn
 */
class App_Ini
{
	/**
	 * @var App Settings
	 */
	private static $_data = array();
	
	/**
	 * Set ini data
	 * 
	 * @param string $name Ini key
	 * @param mixed $val Ini value
	 */
	public static function set($name, $val = null)
	{
		if (is_array($name)) {
			foreach ($name as $key => $val) {
				self::$_data[$key] = $val;
			}
		} else {
			self::$_data[$name] = $val;
		}
	}
	
	/**
	 * Get app ini settings
	 * 
	 * @param string $name Ini key
	 * @return mixed Ini setting
	 * @throws Exception
	 */
	public static function get($name)
	{
		if (isset(self::$_data[$name]))
            return self::$_data[$name];
        else
			return null;
	}
	
	/**
	 * Magic setter method implementation
	 * 
	 * @param string $name Ini key
	 * @param mixed $val Ini setting
	 * @return self
	 */
	public function __set($name, $val)
	{
		self::$_data[$name] = $val;
		return $this;
	}
	
	/**
	 * Magic getter method implementation
	 * 
	 * @param type $name
	 * @return type
	 */
	public function __get($name)
	{
		return self::get($name);
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
				$key = strtolower(substr($method, 3));
				return isset(self::$_data[$key]) ? self::$_data[$key] : null;
			case 'set':
				$key = strtolower(substr($method, 3));
				self::$_data[$key] = $params[0];
				return $this;
			case 'has' :
                $key = $this->_underscore(substr($method,3));
                return isset(self::$_data[$key]);
			case 'uns':
				$key = $this->_underscore(substr($method, 5));
				unset(self::$_data[$key]);
				break;
		}
	}
}