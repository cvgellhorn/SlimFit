<?php

/**
 * Index Controller
 *
 * @author cvgellhorn
 */
class Index extends App_Controller
{
	public function indexAction()
	{
		//$this->redirect('moep/index');
		$db = App_Database::getInstance();
		$result = $db->fetchAssoc('SELECT * FROM `data` WHERE `test` = \'blub\'', 'sdsdh');
		App_Debug::dump($result);

		/*App_Debug::dump($db->insert('data', array(
			'test' => 'nevermind',
			'moep' => 'test',
			'value' => 'eegehrgjehrgj herghegj'
		)));*/

		/*App_Debug::dump($db->delete('data', array(
			'test = ?' => 'chingchang',
			'moep = ?' => new App_Db_Expr('NOW()')
		)));*/
	}
}