<?php

/**
 * Main App Controller
 *
 * @author cvgellhorn
 */
class App_Controller
{
	/**
	 * @const Global controller defaults
	 */
	const DEFAULT_CONTROLLER = 'index';
	const DEFAULT_ACTION = 'index';
	const ACTION_SUFFIX = 'Act';

	/**
	 * @var bool Load action view
	 */
	private $_renderView = true;
	
	/**
	 * @var bool Load layout template
	 */
	private $_useTemplate = true;
	
	/**
	 * @var string Use another view
	 */
	private $_useView;
	
	/**
	 * @var Instance of App_Request
	 */
	protected $request;
	
	/**
	 * @var Instance of App_View
	 */
	protected $view;
	
	/**
	 * Controller Constructor
	 */
	public function __construct()
	{
		$this->request = App_Request::getInstance();
		$this->view = new App_View();
		
		$this->_init();
	}
	
	/**
	 * Constructor for child classes
	 */
	protected function _init() {}
	
	/**
	 * Is called before an action is dispatched
	 */
	public function preDispatch() {}
	
	/**
	 * Is called after an action is dispatched
	 */
	public function postDispatch() {}
	
	/**
	 * Do App authentication
	 */
	public function __doAuth()
	{
		//-- Check Auth
		/*if($this->request->isInternal())
			return;
		
		if (App_Ini::get('auth')) {
			//-- Do auth
			require_once 'App/Auth.php';
			$auth = App_Auth::getInstance();
		}*/
	}
	
	/**
	 * Load action view
	 */
	public function __loadView()
	{
		if ($this->_renderView) {
			$action = (null !== $this->_useView) ? $this->_useView 
					: $this->request->getActionName();
			$controller = $this->request->getControllerName();
			
			if($this->request->isInternal() || !$this->_useTemplate) {
				$this->view->loadView($action, $controller);
			} else {
				$this->view->loadLayoutView($action, $controller);
			}
		}
	}
	
	/**
	 * Set no view renderer
	 */
	protected function setNoRender()
	{
		$this->_renderView = false;
	}
	
	/**
	 * Set no layout template
	 */
	protected function setNoTemplate()
	{
		$this->_useTemplate = false;
	}
	
	/**
	 * Set another view to render
	 * 
	 * @param type $view View to render
	 */
	protected function setUseView($view)
	{
		$this->_renderView = true;	
		$this->_useView = $view;
	}
	
	/**
	 * Redirect to given uri
	 * 
	 * @param type $uri Uri to redirect
	 */
	protected function redirect($uri)
	{
		App_Response::getInstance()->redirect($uri);
	}
}