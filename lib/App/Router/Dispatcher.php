<?php

/**
 * Original Version:
 * 
 * @author Rob Apodaca <rob.apodaca@gmail.com>
 * @copyright Copyright (c) 2009, Rob Apodaca
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link https://github.com/robap/php-router
 * 
 * 
 * Modified Version:
 * 
 * @author cvgellhorn
 */
class App_Router_Dispatcher
{
	/**
	 * The suffix used to append to the class name
	 * 
	 * @var string
	 */
	protected $suffix;

	/**
	 * The path to look for classes (or controllers)
	 * 
	 * @var string
	 */
	protected $classPath;

	/**
	 * Class constructor
	 */
	public function __construct()
	{
		$this->suffix = App_Controller::CONTROLLER_SUFFIX;
	}

	/**
	 * Attempts to dispatch the supplied App_Router_Route object. Returns false if it fails
	 * 
	 * @param App_Router_Route $route
	 * @throws Router_ClassFileNotFoundException
	 * @throws Router_BadClassNameException
	 * @throws Router_ClassNameNotFoundException
	 * @throws Router_ClassMethodNotFoundException
	 * @return mixed - result of controller method or false on error
	 */
	public function dispatch(App_Router_Route $route, $request)
	{
		$c = trim($route->getMapClass());
		$a = trim($route->getMapMethod());
		$arguments = $route->getMapArguments();
		
		//-- Set final default controller and action name
		$controller = ('' === $c) ? App_Controller::DEFAULT_CONTROLLER : $c;
		$action = ('' === $a) ? App_Controller::DEFAULT_ACTION : $a;

		//-- Add controller and action to request
		$request->setControllerName($controller)
				->setActionName($action);
		
		//-- Set action suffix
		$action .= App_Controller::ACTION_SUFFIX;
		
		//-- Because the class could have been matched as a dynamic element,
		// it may only contain alphanumeric characters. Anything not matching
		// the regexp is considered potentially harmful.
		$controller = ucfirst(str_replace('\\', '', $controller));
		preg_match('/^[a-zA-Z0-9_]+$/', $controller, $matches);

		if (count($matches) !== 1)
			throw new Router_BadClassNameException('Disallowed characters in class name ' . $controller);

		//-- Apply the suffix
		$fileName = $this->classPath . $controller . $this->suffix . '.php';
		$controller .= $this->suffix;

		//-- At this point, we are relatively assured that the file name is safe
		// to check for it's existence and require in.
		if (false === file_exists($fileName))
			throw new Router_ClassFileNotFoundException('Class file not found');
		else
			require_once($fileName);
		
		//-- Check for the class class
		if (false === class_exists($controller))
			throw new Router_ClassNameNotFoundException('class not found ' . $controller);

		//-- Check for the method
		if (false === method_exists($controller, $action))
			throw new Router_ClassMethodNotFoundException('method not found ' . $action);

		//-- All above checks should have confirmed that the class can be instatiated
		// and the method can be called
		return $this->dispatchController($controller, $action, $arguments);
	}

	/**
	 * Create instance of controller and dispatch to it's method passing
	 * arguments. Override to change behavior.
	 * 
	 * @param string $controller
	 * @param string $action
	 * @param array $args
	 * @return mixed - result of controller method
	 */
	protected function dispatchController($controller, $action, $args, $context = null)
	{
		$obj = new $controller($context);
		if($obj instanceof App_Controller) {
			$obj->preDispatch();
			$obj->__doAuth();
			$obj->{$action}($args);
			$obj->__loadView();
			$obj->postDispatch();
		} else {
			throw new Router_NoAppControllerException(
				$controller . ' is not an instance of APP_Controller'
			);
		}
	}

	/**
	 * Sets a suffix to append to the class name being dispatched
	 * 
	 * @param string $suffix
	 * @return Dispatcher
	 */
	public function setSuffix($suffix)
	{
		$this->suffix = $suffix;
		return $this;
	}

	/**
	 * Set the path where dispatch class (controllers) reside
	 * 
	 * @param string $path
	 * @return Dispatcher
	 */
	public function setClassPath($path)
	{
		$this->classPath = preg_replace('/\/$/', '', $path) . '/';
		return $this;
	}
}