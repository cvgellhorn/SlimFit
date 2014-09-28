<?php namespace SlimFit;

use SlimFit\Config;
use SlimFit\Request;
use SlimFit\Router;
use SlimFit\Error;

/**
 * Main SlimFit View
 *
 * @author cvgellhorn
 */
class View
{
	/**
	 * Load action view
	 *
	 * @param string $action Current action name 
	 * @param string $controller Current controller name
	 * @throws Error
	 */
	public function loadView($action, $controller)
	{
		$viewPath = APP_DIR.DS.'views'.DS.$controller.DS.$action.'.phtml';

		if (file_exists($viewPath)) {
			require_once($viewPath);
		} else {
			throw new Error('Could not load action view: ' . $controller.'/'.$action
				. '. File does not exist: ' . $action . '.phtml');
		}
	}
	
	/**
	 * Call controller action
	 * 
	 * @param string $action Action name
	 * @param string $controller Controller name
	 * @param mixed $params Action params
	 */
	public function action($action, $controller, $params = [])
	{
		// Add given params into request
		$request = Request::load();
		foreach ($params as $key => $value) {
			$request->setParam($key, $value);
		}
		
		// Route to new controller action
		$request->setIsInternal();
		Router::load()->route($request->setUri(
			Config::get('base_path') . $controller . '/' . $action
		));
	}
}