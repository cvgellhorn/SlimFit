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
	 * @var Controller and action for dynamic layout loading
	 */
	private $_layoutAction;
	private $_layoutController;
	
	/**
	 * Constructor
	 */
	public function __construct()
	{}

	/**
	 * Get action content in default layout
	 */
	private function _getContent()
	{
		$this->loadView($this->_layoutAction, $this->_layoutController);
	}
	
	/**
	 * Load view from controller action
	 *
	 * @param string $action Current action name 
	 * @param string $controller Current controller name
	 * @throws Error
	 */
	public function loadView($action, $controller)
	{
		try {
			$viewPath = APP_DIR . DS .  'views' . DS
				. $controller . DS . $action . '.phtml';
			
			if (file_exists($viewPath)) {
				require_once($viewPath);
			} else {
				throw new Error('File does not exists' . $action . '.phtml');
			}
		} catch (Error $e) {
			throw new Error('Could not load view from action: ' . $action);
		}
	}
	
	/**
	 * Load view from controller action with default layout
	 *
	 * @param string $action Current action name 
	 * @param string $controller Current controller name
	 * @throws Error If main template not exists
	 */
	public function loadLayoutView($action, $controller)
	{
		// Set current controller and action for layout loading
		$this->_layoutAction = $action;
		$this->_layoutController = $controller;
		
		$layoutPath = APP_DIR . DS .  'layout' . DS
			. Config::get('template') . '.phtml';

		if (file_exists($layoutPath)) {
			require_once($layoutPath);
		} else {
			throw new Error('Layout template does not exists: '
				. Config::get('template') . '.phtml');
		}
	}
	
	/**
	 * Load controler action
	 * 
	 * @param string $action Action name
	 * @param string $controller Controller name
	 * @param mixed $params Action params
	 */
	public function action($action, $controller, $params = array())
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