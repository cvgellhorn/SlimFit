<?php

/**
 * SF_Database_Abstract
 *
 * Created by PhpStorm.
 * Date: 16.01.14
 * Time: 19:08
 * User: cgellhorn
 */
abstract class SF_Db_Abstract
{
	protected $_db;

	public function __construct()
	{
		$this->_db = SF_Db::getInstance();
	}
}