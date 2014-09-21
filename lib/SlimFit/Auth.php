<?php

/**
 * Authentication class
 *
 * @author cvgellhorn
 */
class SF_Auth
{
	/**
	 * Instance implementation
	 * 
	 * @var SF_Auth
	 */
	private static $_instance = null;
	
	/**
	 * Auth identity object
	 * 
	 * @var SF_Auth_Identity
	 */
	private $_identity = null;
	
	/**
	 * Auth adapter
	 * 
	 * @var SF_Auth_Db
	 */
	private $_authAdapter = null;
	
	/**
	 * Single pattern implementation
	 * 
	 * @return SF_Auth
	 */
	public static function getInstance()
	{
		if (null === self::$_instance)
			self::$_instance = new self();
		
		return self::$_instance;
	}

	/**
	 * Get current user object
	 *
	 * @return SF_Auth_Identity
	 */
	public static function getUser()
	{
		return self::getInstance()->getIdentity();
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
		if ($this->hasIdentity()) {
			$this->getAdapter()->logout();
		}
	}
	
	/**
	 * Get user identity object
	 * 
	 * @return SF_Auth_Identity
	 */
	public function getIdentity()
	{
		return $this->_identity;
	}
	
	/**
	 * Set identity object
	 * 
	 * @param SF_Auth_Identity $identity Identity object
	 * @return SF_Auth
	 * @throws SF_Exception
	 */
	public function setIdentity(SF_Auth_Identity $identity)
	{
		if ($this->hasIdentity()) {
			throw new SF_Exception('Can\'t set identity of already authenticated user');
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
	 * @return SF_Auth_Db
	 * @throws SF_Exception
	 */
	public function getAdapter()
	{
		if (null === $this->_authAdapter) {
			$adapter = SF_Ini::get('auth')['adapter'];
			switch ($adapter) {
				case 'db':
					$adapterClass = new SF_Auth_Db();
					break;
				default:
					throw new SF_Exception('Unknown Auth Adapter declared in app.config.php');
					break;

			}
			$this->_authAdapter = $adapterClass;
		}
		return $this->_authAdapter;
	}
}