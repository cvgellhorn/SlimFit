<?php

/**
 * Main App View
 *
 * @author cvgellhorn
 */
class App_View
{
	/**
	 * @var Controller and action for dynamic layout loading
	 */
	private $_layoutAction;
	private $_layoutController;
	
	/**
	 * Constructor
	 */
	public function __construct() {}

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
	 */
	public function loadView($action, $controller)
	{
		try {
			$viewPath = APP_PATH . DS .  'views' . DS 
				. $controller . DS . $action . '.phtml';
			
			if (file_exists($viewPath)) {
				require_once $viewPath;
			} else {
				throw new App_Exception('File does not exists' . $action . '.phtml', 3334);
			}
		} catch (App_Exception $e) {
			throw new App_Exception('Could not load view from action: ' . $action, 3333);
		}
	}
	
	/**
	 * Load view from controller action with default layout
	 *
	 * @param string $action Current action name 
	 * @param string $controller Current controller name
	 */
	public function loadLayoutView($action, $controller)
	{
		//-- Set current controller and action for layout loading
		$this->_layoutAction = $action;
		$this->_layoutController = $controller;
		
		$layoutPath = APP_PATH . DS .  'layout' . DS 
			. App_Ini::get('template') . '.phtml';

		if (file_exists($layoutPath)) {
			require_once $layoutPath;
		} else {
			throw new App_Exception('Layout template does not exists: ' 
				. App_Ini::get('template') . '.phtml', 3334);
		}
	}
	
	/**
	 * Load controler action
	 * 
	 * @param string Action name
	 * @param string Controller name
	 * @param mixed Given action params
	 */
	public function action($action, $controller, $params = array())
	{
		//-- Add given params into request
		$request = App_Request::getInstance();
		foreach ($params as $key => $value) {
			$request->setParam($key, $value);
		}
		
		//-- Route to new controller action
		$request->setIsInternal();
		App_Router::getInstance()->route($request->setUri(
				App_Ini::get('base_path') . $controller . '/' . $action
			));
	}
}