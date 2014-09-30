<?php namespace SlimFit;

/**
 * Autoloader
 *
 * @author cvgellhorn
 */
class Autoloader
{
	/**
	 * Indicates if the autoloader has been registered
	 * 
	 * @var bool
	 */
	private static $_registered = false;

	/**
	 * Global class aliases
	 *
	 * @var array
	 */
	private static $_classAliases = [
		'SF'     => '\SlimFit\SF',
		'Config' => '\SlimFit\Config'
	];

	/**
	 * Register global class aliases for easier access
	 */
	private static function _registerAliases()
	{
		foreach (self::$_classAliases as $alias => $class) {
			class_alias($class, $alias);
		}
	}

	/**
	 * Register the autoloader
	 */
	public static function register()
	{
		if (!self::$_registered) {
			self::$_registered = spl_autoload_register(['self', 'load']);
			self::_registerAliases();
		}
	}

	/**
	 * Get the normal file path for a class
	 *
	 * @param string $class
	 * @return string Full file path
	 */
	public static function normalizeClass($class)
	{
		// Trim unnecessary backslash
		if ($class[0] == '\\') $class = substr($class, 1);

		return str_replace(['\\', '_'], DS, $class) . '.php';
	}

	/**
	 * Load the given class file
	 * 
	 * @param string $class Class name
	 */
	public static function load($class)
	{
		require_once(self::normalizeClass($class));
	}
}