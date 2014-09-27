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
		$layout = $this->loadLayout();
		$layout->addCSS(['main' => 'css/main.css']);
	}
}