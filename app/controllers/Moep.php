<?php

/**
 * Moep Controller
 *
 * @author cvgellhorn
 */
class Moep extends SF_Controller
{	
	public function indexAction($test)
	{
		SF_Debug::dump($this->request->getParam('id'));
		
		SF_Debug::dump($test);
		SF_Debug::dump('INDEX ACTION');
	}
	
	public function testAction($args)
	{
		$this->view->teeeeestValue = '999999999999999';
		
		$adapter = SF_Auth::getInstance()->getAdapter();
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