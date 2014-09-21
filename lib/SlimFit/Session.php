<?php namespace SlimFit;

/**
 * Main Session
 *
 * @author cvgellhorn
 */
class Session
{
	/**
	 * Check whether or not the session was started
	 *
	 * @var bool
	 */
	private static $_sessionStarted = false;

	/**
	 * Whether or not session has been destroyed via session_destroy()
	 *
	 * @var bool
	 */
	private static $_destroyed = false;

	/**
	 * Whether or not write close has been performed.
	 *
	 * @var bool
	 */
	private static $_writeClosed = false;

	/**
     * Default number of seconds the session will be remembered 
	 * for when asked to be remembered
     *
	 * Default 2 weeks
	 *
     * @var int
     */
    private static $_rememberMeSeconds = 1209600;

	private function __construct()
	{
		session_cache_expire(self::$_rememberMeSeconds);
	}

	public static function start()
	{
		session_start();
	}

	public static function destroy()
	{
		session_destroy();
	}

	public static function setStorage($key, $val)
	{
		$_SESSION[$key] = $val;
	}
	
	public static function getStorage($key)
	{
		return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
	}

	public static function unsetStorage($key)
	{
		unset($_SESSION[$key]);
	}

	public static function clearStorage()
	{
		session_unset();
	}
}