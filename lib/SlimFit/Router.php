<?php namespace SlimFit;

use SlimFit\Config;

/**
 * Global Router
 * 
 * @author cvgellhorn
 */
class Router
{
	/**
	 * Single pattern implementation
	 * 
	 * @return Router Instance
	 */
	private static $_instance = null;

	/**
	 * Routes storage
	 *
	 * @var array
	 */
	private $_routes = [];

	/**
	 * The path to look for controllers
	 *
	 * @var string
	 */
	public $classPath;
	
	/**
	 * Single pattern implementation
	 *
	 * @return Router Instance
	 */
	public static function load()
	{
		if (null === self::$_instance) {
			self::$_instance = new self();
		}
		
		return self::$_instance;
	}
	
	/**
	 * Register routes
	 */
	private function __construct()
	{
		$basePath = Config::get('base_path');

		// Main route: slimfit.com
		$mainRoute = [rtrim($basePath, '/') => []];

		// Controller route: slimfit.com/user
		$controllerRoute = [
			$basePath . '{:class}' => ['class']
		];

		// Action route: slimfit.com/user/add
		$actionRoute = [
			$basePath . '{:class}/{:method}' => ['class', 'method']
		];

		// Param route: slimfit.com/user/get/23
		$paramRoute = [
			$basePath . '{:class}/{:method}/{:id}' => ['class', 'method', 'id']
		];

		$this->addRoutes([$mainRoute, $controllerRoute,	$actionRoute, $paramRoute]);
		$this->classPath = realpath(APP_DIR . DS . 'controllers');
	}

	private function _findeRoute($uri)
	{
		$uri = rtrim($uri, '/');
		foreach ($this->_routes as $route) {
			if ($this->matchMap($uri)) {
				return $route;
			}
		}
	}

	private function _dispatch($route, $request)
	{

	}

	public function addRoute($url, $elements = [])
	{
		$this->_routes[$url] = $elements;
	}

	public function addRoutes($routes)
	{
		foreach ($routes as $url => $elements) {
			$this->_routes[$url] = $elements;
		}
	}

	/**
	 * Do routing
	 * 
	 * @param Request $request Request object
	 */
	public function route($request = null)
	{
		try {
			// Try loading route from uri
			$route = $this->_findeRoute($request->getUri());

			// Try dispatching controller action
			$this->_dispatch($route, $request);
		} catch (Error $exception) {

		}
	}
}