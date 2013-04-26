<?php

/**
 * Database authentication adapter
 *
 * @author cvgellhorn
 */
class App_Auth_Adapter_Db implements App_Auth_Adapter_Interface
{
	/**
	 * Database Connection
	 *
	 * @var App_Db_Adapter_Abstract
	 */
	protected $_dbAdapter = null;

	/**
	 * Table to perform a select on
	 *
	 * @var string
	 */
	protected $_tableName = null;

	/**
	 * Columns to be used as the credentials
	 *
	 * @var array
	 */
	protected $_credentials = array();

	/**
	 * Set the login data such as username and password
	 * 
	 * @param array $data Array of login credentials
	 * @return App_Auth_Adapter_Db
	 */
	public function setLoginData(array $data)
	{
		$this->_credentials = $data;
		return $this;
	}
	
	/**
	 * Peform an login attempt
	 */
	public function login()
	{
		if(empty($this->_credentials)) {
			throw new App_Auth_Adapter_Exception('The user credentials must be supplied.');
		}
		
		try {
			//-- TODO: perform a databse authentication with given params
			App_Auth::getInstance()->setIdentity(new App_Auth_Identity($userData));
		} catch(Exception $e) {
			throw new App_Auth_Adapter_Exception('Failed authenticating user.');
		}
	}

	/**
	 * Peform an logout attempt
	 */
	public function logout()
	{
		//-- TODO: Destroy session and session storage data
		App_Auth::getInstance()->clearIdentity();
	}
}