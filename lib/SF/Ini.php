<?php

/**
 * SF Initializer
 * 
 * @author cvgellhorn
 */
class SF_Ini
{
	/**
	 * @var SF Settings
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
		if (isset(self::$_data[$name])) {
            return self::$_data[$name];
		} else {
			return null;
		}
	}
	
	/**
	 * Magic setter method implementation
	 * 
	 * @param string $name Ini key
	 * @param mixed $val Ini setting
	 * @return SF_Ini
	 */
	public function __set($name, $val)
	{
		self::$_data[$name] = $val;
		return $this;
	}
	
	/**
	 * Magic getter method implementation
	 * 
	 * @param string $name
	 * @return string
	 */
	public function __get($name)
	{
		return self::get($name);
	}
}