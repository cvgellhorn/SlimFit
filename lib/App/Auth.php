<?php

/**
 * Authentication class
 *
 * @author cvgellhorn
 */
class App_Auth
{
	/**
	 * Instance implementation
	 * 
	 * @var App_Auth
	 */
	private static $_instance = null;
	
	/**
	 * Auth identity object
	 * 
	 * @var App_Auth_Identity
	 */
	private $_identity = null;
	
	/**
	 * Auth adapter
	 * 
	 * @var App_Auth_Adapter_Interface
	 */
	private $_authAdapter = null;
	
	/**
	 * Single pattern implementation
	 * 
	 * @return Instance of App_Request
	 */
	public static function getInstance()
	{
		if (null === self::$_instance)
			self::$_instance = new self();
		
		return self::$_instance;
	}
	
	/**
	 * Private construct cause single pattern implementation
	 */
	private function __construct() {}
	
	/**
	 * Private clone cause single pattern implementation
	 */
	private function __clone() {}
	
	/**
	 * Peform an logout attempt
	 */
	public function logout()
	{
		if($this->hasIdentity()) {
			$this->getAdapter()->logout();
		}
	}
	
	/**
	 * Get user identity object
	 * 
	 * @return App_Auth_Identity
	 */
	public function getIdentity()
	{
		return $this->_identity;
	}
	
	/**
	 * Set identity object
	 * 
	 * @param App_Auth_Identity $identity Identity object
	 * @return App_Auth
	 */
	public function setIdentity(App_Auth_Identity $identity)
	{
		if($this->hasIdentity()) {
			throw new App_Auth_Adapter_Exception('Can\'t set identity of already authenticated user');
		}
		
		$this->_identity = $identity;
		return $this;
	}
	
	/**
	 * Returns true if an identity is available
	 * 
	 * @return bool
	 */
	public function hasIdentity()
	{
		return (null === $this->_identity) ? false : true;
	}
	
	/**
	 * Clear auth identity object
	 */
	public function clearIdentity()
	{
		$this->_identity = null;
	}
	
	/**
	 * Get the current authentication adapter
	 * 
	 * @return App_Auth_Adapter_Interface
	 */
	public function getAdapter()
	{
		if (null === $this->_authAdapter) {
			$adapter = App_Ini::get('auth_adapter');	
			switch($adapter) {
				case 'db':
					$adapterClass = new App_Auth_Adapter_Db();
					break;
				case 'ldap':
					$adapterClass = new App_Auth_Adapter_Ldap();
					break;
				default:
					$adapterClass = new App_Auth_Adapter_Db();
					break;
			}
			$this->_authAdapter = $adapterClass;
		}
		return $this->_authAdapter;
	}
}