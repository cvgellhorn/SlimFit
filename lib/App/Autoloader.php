<?php

/**
 * Autoloader
 *
 * @author cvgellhorn
 */
class App_Autoloader
{
	/**
	 * Instance exists state
	 * 
	 * @var bool
	 */
	private static $_instance = false;
	
	/**
	 * Single pattern implementation
	 * 
	 * @return Instance of App_Request
	 */
	public static function register()
	{
		if (false === self::$_instance) {
			spl_autoload_register(array('self', 'loader'));
			self::$_instance = true;
		}
	}
	
	/**
	 * Autoloader method
	 * 
	 * @param string $class Class name
	 */
	private static function loader($class)
	{
		require_once str_replace('_', DS, $class) . '.php';
	}
}