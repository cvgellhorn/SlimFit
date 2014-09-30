<?php namespace SlimFit;

use SlimFit\Config;
use SlimFit\Request;
use SlimFit\Router;

/**
 * Main SlimFit class
 *
 * Class alias registerd in \SlimFit\Autoloader
 * 
 * @author cvgellhorn
 */
final class SF
{
	/**
	 * Application log files
	 */
	const LOG_FILE_SYSTEM = 'system.log';
	const LOG_FILE_DEBUG  = 'debug.log';
	const LOG_FILE_ERROR  = 'error.log';

	/**
	 * Private array of instances
	 *
	 * @var array
	 */
	private static $_instances = [];
	
	/**
	 * Check if application is in dev mode
	 *
	 * @return bool Development mode state
	 */
	public static function dev()
	{
		return (APP_ENV == 'development') ? true : false;
	}
	
	/**
	 * Global singleton implementation pattern
	 *
	 * @param string $class Class name
	 * @param mixed $data Instance data
	 * @return object Object of $class
	 */
	public static function getInstance($class, $data = null)
	{
		if (!isset(self::$_instances[$class])) {
			self::$_instances[$class] = ($data) ? new $class($data) : new $class();
		}
		
		return self::$_instances[$class];
	}
	
	/**
	 * Unset or unregister Instances
	 *
	 * @param string $class Class name
	 */
	public static function destructInstance($class)
	{
		if (isset(self::$_instances[$class])) {
			if (is_object(self::$_instances[$class])
			&& (method_exists(self::$_instances[$class], '__destruct'))) {
				self::$_instances[$class]->__destruct();
			}
			
			unset(self::$_instances[$class]);
		}
	}

	/**
	 * Write log message
	 *
	 * @param string $msg Log message
	 * @param string $file Filename
	 */
	public static function log($msg, $file = self::LOG_FILE_SYSTEM)
	{
		$filePath = BASE_DIR . 'logs' . DS . $file;

		// Create directory recursive if not exists
		$pathinfo = pathinfo($filePath);
		if (!is_dir($pathinfo['dirname'])) {
			@mkdir($pathinfo['dirname'], 0777, true);
		}

		$newFile = !file_exists($filePath);

		// Write log
		$content = date('Y-m-d H:i:s') . ': ' . $msg . PHP_EOL;
		@file_put_contents($filePath, $content, FILE_APPEND);

		// Change new file permission
		if ($newFile) @chmod($filePath, 0777);
	}

	/**
	 * Run Application
	 */
	public static function run()
	{
		// Set application config
		Config::init();

		// Route to controller action
		Router::load()->route(Request::load());
	}
}