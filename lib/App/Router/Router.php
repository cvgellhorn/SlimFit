<?php

/**
 * Original Version:
 * 
 * @author Rob Apodaca <rob.apodaca@gmail.com>
 * @copyright Copyright (c) 2009, Rob Apodaca
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link http://robap.github.com/php-router/
 * 
 * 
 * Modified Version:
 * 
 * @author cvgellhorn
 */
class App_Router_Router
{
	/**
	 * Stores the Route objects
	 * @var array
	 */
	private $_routes = array();

	/**
	 * A prefix to prepend when calling getUrl()
	 * @var string
	 */
	private $_prefix = '';

	/**
	 * Object constructor. Optionally pass array of routes to add
	 * 
	 * @param array[string]Route $routes 
	 */
	public function __construct($routes = array())
	{
		$this->addRoutes($routes);
	}

	/**
	 *
	 * @param string $prefix 
	 */
	public function setPrefix($prefix)
	{
		$this->_prefix = $prefix;
	}

	/**
	 * Adds a named route to the list of possible routes
	 * @param string $name
	 * @param Route $route
	 * @return App_Router_Router
	 */
	public function addRoute($name, $route)
	{
		$this->_routes[$name] = $route;
		return $this;
	}

	/**
	 * Adds an array of named routes to the list of possible routes
	 * 
	 * @param array[string]Route $routes
	 * @return App_Router_Router
	 */
	public function addRoutes($routes)
	{
		$this->_routes = array_merge($this->_routes, $routes);
		return $this;
	}

	/**
	 * Returns the routes array
	 * @return [Route]
	 */
	public function getRoutes()
	{
		return $this->_routes;
	}

	/**
	 * Builds and gets a url for the named route
	 * @param string $name
	 * @param array $args
	 * @param bool $prefixed
	 * @throws Router_NamedPathNotFoundException
	 * @throws InvalidArgumentException
	 * @return string the url
	 */
	public function getUrl($name, $args = array(), $prefixed = true)
	{
		if (true !== array_key_exists($name, $this->_routes))
			throw new Router_NamedPathNotFoundException;
		
		//-- Check for the correct number of arguments
		$matchOk = (count($args) !== count($this->_routes[$name]->getDynamicElements())) ? false : true;

		/*
		 * This will assure arguments that are more specific are replaced before.
		 * That's important as if we have a route /:some/:something and 
		 * we input :some before :something in the $args arrau :something's :some will also be replaced.
		 */
		if (!function_exists('sortMoreSpecific')) {
			function sortMoreSpecific($a, $b)
			{
				return (strlen($b) - strlen($a));
			}
		}
		uksort($args, 'sortMoreSpecific');

		$path = $this->_routes[$name]->getPath();
		foreach ($args as $argKey => $argValue) {
			$path = str_replace($argKey, $argValue, $path, $count);
			if (1 !== $count)
				$matchOk = false;
		}

		//-- Check that all of the argument keys matched up with the dynamic elements
		if (false === $matchOk)
			throw new InvalidArgumentException;

		if ($prefixed)
			return $this->_prefix . $path;
		else
			return $path;
	}

	/**
	 * Finds a maching route in the routes array using specified $path
	 * @param string $uri Reques uri
	 * @return App_Router_Route
	 * @throws Router_RouteNotFoundException
	 */
	public function findRoute($uri)
	{
		$uri = rtrim($uri, '/');
		foreach ($this->_routes as $route) {
			if (true === $route->matchMap($uri)) {
				return $route;
			}
		}
		throw new Router_RouteNotFoundException;
	}
}