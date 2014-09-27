<?php

use SlimFit\Controller;
use SlimFit\Debug;

/**
 * Index Controller
 *
 * @author cvgellhorn
 */
class IndexController extends Controller
{
	protected function init()
	{
		$this->loadLayout();
	}

	public function indexAction()
	{
		// Set no view renderer
		$this->setNoRender();

		// Set no view and layout renderer (for ajax calls)
		$this->setNoRender(true);

		// Set no layout renderer
		$this->loadLayout()->setNoRender();

		// Add css to layout header
		$this->loadLayout()->addCSS(['main' => 'css/main.css']);
	}
}