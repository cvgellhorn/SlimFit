<?php

/**
 * Moep Controller
 *
 * @author cvgellhorn
 */
class Moep extends App_Controller
{	
	public function indexAct()
	{
		App_Debug::dump('INDEX ACTION');
	}
	
	public function testAct($args)
	{
		$this->view->teeeeestValue = '999999999999999';
		
		/*App_Debug::dump('arguments: ');
		App_Debug::dump($args);
		App_Debug::dump($this->request->isAjaxRequest());*/
	}
}