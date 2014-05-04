<?php

/**
 * Main SlimFit class
 * 
 * @author cvgellhorn
 */
final class SF
{
	/**
	 * Application log files
	 */
	const LOG_FILE_SYSTEM	= 'system.log';
	const LOG_FILE_DEBUG	= 'debug.log';
	const LOG_FILE_ERROR	= 'error.log';
	
    /**
     * Private array of instances
     * 
     * @var array
     */
    private static $_instances = array();
    
	/**
	 * Private loggers
	 *
	 * @var array
	 */
	private static $_loggers = array();
		
	/**
     * Private array of translations
     * 
     * @var array
     */
    private static $_translations = array();
    
	/**
     * Get SlimFit version
     * 
     * @param bool $implode
     * @return string SF lib version number
     */
    public static function getVersion($implode)
    {
		$version = array(
            'major'     => '0',
            'minor'     => '1',
            'revision'  => '0',
            'patch'     => '0'
        );
		
		return ($implode) ? implode('.', $version) : $version;
    }
	
	/**
	 * Main getBaseDir function
	 *
	 * @param string $path Path addon
	 * @return string Application base dir
	 */
	public static function getBaseDir($path = null)
	{
		return ($path) ? APP_PATH . DS . '..' . DS . $path : 
			APP_PATH . DS . '..' . DS;
	}
	
	/**
	 * Get is dev mode
	 *
	 * @return bool Development mode state
	 */
	public static function getDev()
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
		if (!isset(self::$_instances[$class]))
			self::$_instances[$class] = ($data) ? new $class($data) : new $class();
		
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
			if (is_object(self::$_instances[$class]) && (method_exists(self::$_instances[$class], '__destruct')))
				self::$_instances[$class]->__destruct();
			
			unset(self::$_instances[$class]);
		}
	}
	
	/**
	 * Load lib class and get Instance
	 * 
	 * @param string $class Given lib class name
	 * @param string $type Class type
	 * @param mixed $data Object data
	 * @param bool $instance Get singleton or object
	 * @return mixed Object of given name
	 * @throws SF_Exception
	 */
	public static function getClass($class, $type, $data = null, $instance = false)
	{
		// Example: SF/Router/Dispatcher.php
		$file = str_replace('_', DS, $class) . '.php';
		
		switch($type) {
			case 'lib':
				$filePath = self::getBaseDir() . 'lib' . DS . $file;
				break;
			case 'controllers':
			case 'models':
				$filePath = APP_PATH . DS . $type . DS . $file;
				$file = $filePath;
				break;
			default:
				$filePath = APP_PATH . DS . $type . DS . $file;
				$file = $filePath;
				break;
		}
		
		if(file_exists($filePath)) {
			try {
				require_once $file;

				if ($instance)
					return self::getInstance($class, $data);

				if ($data) {
					return new $class($data);
				} else {
					return new $class();
				}
			} catch (Exception $e) {
				throw new SF_Exception('Required class could not be loaded', 9998);
			}
		} else {
			throw new SF_Exception('Required class does not exists', 9999);
		}
	}
	
	/**
	 * Load lib class and get Instance
	 * 
	 * @param string $class Given lib class name
	 * @param mixed $data Instance data
	 * @param bool $instance Get singleton or object
	 * @return object Class of given name
	 */
	public static function getLib($class, $data = null, $instance = false)
	{
		return self::getClass($class, 'lib', $data, $instance);
	}
	
	/**
	 * Load lib class and get Instance
	 * 
	 * @param string $class Given lib class name
	 * @param mixed $data Instance data
	 * @param bool $instance Get singleton or object
	 * @return object Class of given name
	 */
	public static function getController($class, $data = null, $instance = false)
	{
		return self::getClass($class, 'controllers', $data, $instance);
	}
	
	/**
	 * Load lib class and get Instance
	 * 
	 * @param string $class Given lib class name
	 * @param mixed $data Instance data
	 * @param bool $instance Get singleton or object
	 * @return object Class of given name
	 */
	public static function getModel($class, $data = null, $instance = false)
	{
		return self::getClass($class, 'models', $data, $instance);
	}
	
	/**
	 * Get global logger
	 * 
	 * @param string $file Log file (logger)
	 * @param bool $addFirebug Add firebug streamwriter
	 * @return object Zend_Log
	 */
	public static function getLogger($file = self::LOG_FILE_SYSTEM, $addFirebug = false)
	{
		if (!isset(self::$_loggers[$file])) {
			$logDir = self::getBaseDir() . 'data' . DS . 'logs';
			$logFile = $logDir . DS . $file;

			if (!is_dir($logDir))
				mkdir($logDir, 0777);
			
			if (!file_exists($logFile)) {
				file_put_contents($logFile, '');
				chmod($logFile, 0777);
			}
			
			$writer = new Zend_Log_Writer_Stream($logFile);
			$logger = new Zend_Log($writer);

			self::$_loggers[$file] = $logger;
		}
		return self::$_loggers[$file];
	}
	
	/**
	 * Main logging function
	 *
	 * @param string $message
	 * @param int $level
	 * @param string $file
	 */
	public static function log($message, $level = Zend_Log::DEBUG, $file = self::LOG_FILE_SYSTEM)
	{
		try {
			$logger = self::getLogger($file);
			if (is_array($message) || is_object($message))
				$message = print_r($message, true);

			$logger->log($message, $level);
		} catch (Exception $e) {}
	}

	/**
	 * Run Application
	 */
	public static function run()
	{
		require_once 'SF/Autoloader.php';
		SF_Autoloader::register();

		SF_Ini::set(require APP_PATH . '/config/application.config.php');
		SF_Router::getInstance()->route(SF_Request::getInstance());
	}
}