<?php namespace SlimFit;

use SlimFit\Error;

/**
 * SlimFit Configuration
 * 
 * @author cvgellhorn
 */
class Config
{
	/**
	 * Application environments
	 */
	const ENV_LIVE    = 'production';
	const ENV_STAGING = 'staging';
	const ENV_TESTING = 'testing';
	const ENV_DEV     = 'development';

	/**
	 * Configuration filename
	 */
	const FILENAME = 'app.config.php';

	/**
	 * @var array Application config
	 */
	private static $_data = [];

	/**
	 * Initialize application config
	 */
	public static function init()
	{
		// Global alias for faster access
		class_alias('\SlimFit\Config', 'Config');

		$file = APP_DIR . DS . 'config' . DS . self::FILENAME;
		$config = require_once($file);

		if (!isset($config[self::ENV_LIVE])) {
			throw new Error('No production configuration found');
		}

		// Merge environment config data
		if (APP_ENV != self::ENV_LIVE) {
			self::$_data = array_replace_recursive(
				$config[self::ENV_LIVE],
				$config[APP_ENV]
			);
		} else {
			self::$_data = $config[APP_ENV];
		}
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
	public static function get($name = null)
	{
		if (null === $name) return self::$_data;

		return (isset(self::$_data[$name])) ? self::$_data[$name] : null;
	}
}