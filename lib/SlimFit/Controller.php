<?php namespace SlimFit;

use SlimFit\Request;
use SlimFit\Response;
use SlimFit\Layout;
use SlimFit\View;

/**
 * Main SlimFit Controller
 *
 * @author cvgellhorn
 */
class Controller
{
	/**
	 * @const Global controller defaults
	 */
	const DEFAULT_CONTROLLER = 'index';
	const DEFAULT_ACTION     = 'index';
	const ACTION_SUFFIX      = 'Action';
	const CONTROLLER_SUFFIX  = 'Controller';

	/**
	 * @var bool Load action view
	 */
	private $_renderView = true;
	
	/**
	 * @var string Use another view
	 */
	private $_useView;
	
	/**
	 * @var Request
	 */
	protected $request;

	/**
	 * @var Layout
	 */
	protected $layout;
	
	/**
	 * @var View
	 */
	protected $view;
	
	/**
	 * Main controller constructor
	 */
	public function __construct()
	{
		$this->request = Request::load();
		$this->view    = new View();

		// Call child constructor
		$this->init();
	}

	/**
	 * Load action view
	 */
	public function __loadView()
	{
		if ($this->_renderView) {
			$controller = $this->request->getControllerName();
			$action = (null === $this->_useView)
				? $this->request->getActionName()
				: $this->_useView;

			// Load template from current view
			$this->view->loadView($action, $controller);
		}
	}

	/**
	 * Is called before an action is dispatched
	 */
	public function preDispatch()
	{}

	/**
	 * Is called after an action is dispatched
	 */
	public function postDispatch()
	{}

	/**
	 * Constructor for child classes
	 */
	protected function init()
	{}

	/**
	 * Load the current layout and cache it
	 *
	 * @return Layout
	 */
	protected function loadLayout()
	{
		if (null === $this->layout) {
			$this->layout = Layout::load();
		}

		return $this->layout;
	}

	/**
	 * Set no view renderer (plus no layout renderer)
	 *
	 * @param bool $noLayoutRender Set no layout renderer
	 */
	protected function setNoRender($noLayoutRender = false)
	{
		$this->_renderView = false;

		if ($noLayoutRender) {
			$this->loadLayout()->setNoRender();
		}
	}
	
	/**
	 * Set another view to render
	 * 
	 * @param string $view View to render
	 */
	protected function useView($view)
	{
		$this->_renderView = true;	
		$this->_useView    = $view;
	}
	
	/**
	 * Redirect to given uri
	 * 
	 * @param string $uri Uri to redirect
	 */
	protected function redirect($uri)
	{
		Response::load()->redirect($uri);
	}
}