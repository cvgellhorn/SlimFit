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
		$db = App_Db::getInstance();
		$result = $db->fetchAssoc('SELECT * FROM `data` WHERE `test` = \'blub\'', 'sdsdh');
		App_Debug::dump($result);

		/*App_Debug::dump($db->insert('data', array(
			'test' => 'nevermind',
			'moep' => 'test',
			'value' => new App_Db_Expr('NOW()')
		)));*/

		App_Debug::dump($db->query("INSERT INTO `data` (moep, test, value) VALUES ('blub', 'qwerty', 'h eheh erh erher')"));
		//App_Debug::dump($db->query("UPDATE `data` SET moep = 'teeeer', `value` = 'dfsdfsdgs' WHERE moep = 'check' AND `value` = 'blaaaaa'"));
		App_Debug::dump($db->query("SELECT moep FROM data WHERE moep = 'teeeer'"));
		App_Debug::dump('-------------------------------------');
		App_Debug::dump($db->query("SELECT * FROM data WHERE moep = 'teeeer'"));
		App_Debug::dump('-------------------------------------');
		App_Debug::dump($db->query("SELECT * FROM data"));
		App_Debug::dump('-------------------------------------');
		App_Debug::dump($db->query("SELECT * FROM data WHERE moep = 'sdhgfsdhgjksdhgjsdhjksghjk'"));

		/*App_Debug::dump($db->delete('data', array(
			'test = ?' => 'chingchang',
			'moep = ?' => new App_Db_Expr('NOW()')
		)));*/
	}
}