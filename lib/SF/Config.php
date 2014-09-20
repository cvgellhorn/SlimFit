<?php namespace SF;

/**
 * SF Initializer
 * 
 * @author cvgellhorn
 */
class Config
{
	const FILENAME = 'app.config.php';

	/**
	 * @var array Application config
	 */
	private static $_data = array();

	/**
	 * Initialize application config
	 *
	 * TODO: merge production and development data
	 */
	public static function init()
	{
		$configFile = APP_DIR . DS . 'config' . DS . self::FILENAME;
		self::$_data = require_once($configFile);

		// Global alias for faster access
		class_alias('\SF\Config', 'Config');
	}

	/**
	 * Set config data
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
	 * Get config data
	 * 
	 * @param string $name Ini key
	 * @return mixed Ini setting
	 */
	public static function get($name)
	{
		return (isset(self::$_data[$name])) ? self::$_data[$name] : null;
	}
}