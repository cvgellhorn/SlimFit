<?php

//-- Autoloader doesn't grap router exceptions
require_once 'App/Router/Exception.php';

/**
 * Global Router
 * 
 * @author cvgellhorn
 */
class App_Router
{
	/**
	 * Single pattern implementation
	 * 
	 * @return Instance of App_Router
	 */
	private static $_instance = null;
	
	/**
	 * App router
	 * 
	 * @var App_Router_Router
	 */
	private $_router;
	
	/**
	 * Dispatcher
	 * 
	 * @var App_Router_Dispatcher
	 */
	private $_dispatcher;
	
	/**
	 * Single pattern implementation
	 * 
	 * @param App_Router_Route $route Defined allowed route
	 * @return Instance of App_Router
	 */
	public static function getInstance($route = null)
	{
		if (null === self::$_instance)
			self::$_instance = new self($route);
		
		return self::$_instance;
	}
	
	/**
	 * Register routes
	 * 
	 * @param App_Router_Route $route
	 */
	private function __construct($route = null)
	{
		$this->_router = new App_Router_Router;
		if (null === $route) {
			$mainUri = App_Ini::get('base_path');
			$controllerRoute = new App_Router_Route($mainUri . ':class');
			$controllerRoute->addDynamicElement(':class', ':class');
			
			$actionRoute = new App_Router_Route($mainUri . ':class/:method');
			$actionRoute->addDynamicElement(':class', ':class')
				->addDynamicElement(':method', ':method');
			
			$route = new App_Router_Route($mainUri . ':class/:method/:id');
			$route->addDynamicElement(':class', ':class')
				->addDynamicElement(':method', ':method')
				->addDynamicElement(':id', ':id');
			
			$this->_router->addRoutes(array(
				'_default_'			=> new App_Router_Route($mainUri),
				'_controller_'		=> $controllerRoute,
				'_action_'			=> $actionRoute,
				'_actionWithParam_'	=> $route
			));
		} else {
			if (is_array($route)) {
				foreach ($route as $key => $r) {
					$this->_router->addRoute($key, $r);
				}
			} else {
				$this->_router->addRoute('_default_', $route);
			}
		}
		
		$this->_dispatcher = new App_Router_Dispatcher;
		$this->_dispatcher->setClassPath(realpath(APP_PATH . DS . 'controllers'));
	}
	
	/**
	 * Private clone cause single pattern implementation
	 */
	private function __clone() {}
	
	/**
	 * Get current router
	 * 
	 * @return App_Router_Router
	 */
	public function getRouter()
	{
		return $this->_router;
	}
	
	/**
	 * Get current dispatcher
	 * 
	 * @return App_Router_Dispatcher
	 */
	public function getDispatcher()
	{
		return $this->_dispatcher;
	}
	
	/**
	 * Run routing
	 * 
	 * @param App_Request $request Request object
	 */
	public function route($request = null)
	{
		try {
			$this->_dispatcher->dispatch(
				$this->_router->findRoute($request->getUri()),
				$request
			);
		} catch (Router_RouteNotFoundException $exception) {
			echo 'Router_RouteNotFoundException --- HIER UMLEITEN';
		} catch (Router_BadClassNameException $exception) {
			echo 'Router_BadClassNameException --- HIER UMLEITEN';
		} catch (Router_ClassFileNotFoundException $exception) {
			echo 'Router_ClassFileNotFoundException --- HIER UMLEITEN';
		} catch (Router_ClassMethodNotFoundException $exception) {
			echo 'Router_ClassMethodNotFoundException --- HIER UMLEITEN';
		}
	}
}