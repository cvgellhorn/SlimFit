<?php

/**
 * Moep Controller
 *
 * @author cvgellhorn
 */
class Moep extends App_Controller
{	
	public function indexAction($test)
	{
		App_Debug::dump($this->request->getParam('id'));
		
		App_Debug::dump($test);
		App_Debug::dump('INDEX ACTION');
	}
	
	public function testAction($args)
	{
		$this->view->teeeeestValue = '999999999999999';
		
		$adapter = App_Auth::getInstance()->getAdapter();
		$adapter->setLoginData(array(
			'username'	=> 'test',
			'password'	=> 'dhdhdhh4df4fdj24'
		));
		
		try {
			$adapter->login();
		} catch (App_Auth_Adapter_Exception $e) {
			//-- Perform something
		}
	}
}