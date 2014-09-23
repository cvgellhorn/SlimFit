<?php namespace SlimFit;

use SlimFit\Config;
use SlimFit\Error;

/**
 * Authentication class
 *
 * @author cvgellhorn
 */
class Auth
{
	/**
	 * Instance implementation
	 * 
	 * @var Auth
	 */
	private static $_instance = null;
	
	/**
	 * Auth identity object
	 * 
	 * @var Auth_Identity
	 */
	private $_identity = null;
	
	/**
	 * Auth adapter
	 * 
	 * @var Auth_Db
	 */
	private $_authAdapter = null;
	
	/**
	 * Single pattern implementation
	 * 
	 * @return Auth
	 */
	public static function load()
	{
		if (null === self::$_instance) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Get current user object
	 *
	 * @return Auth_Identity
	 */
	public static function getUser()
	{
		return self::load()->getIdentity();
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
	 * @return Auth_Identity
	 */
	public function getIdentity()
	{
		return $this->_identity;
	}
	
	/**
	 * Set identity object
	 * 
	 * @param Auth_Identity $identity Identity object
	 * @return Auth
	 * @throws Error
	 */
	public function setIdentity(Auth_Identity $identity)
	{
		if ($this->hasIdentity()) {
			throw new Error('Can\'t set identity of already authenticated user');
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
	 * @return Auth_Db
	 * @throws Error
	 */
	public function getAdapter()
	{
		if (null === $this->_authAdapter) {
			$adapter = Config::get('auth')['adapter'];
			switch ($adapter) {
				case 'db':
					$adapterClass = new Auth_Db();
					break;
				default:
					throw new Error('Unknown Auth Adapter declared in app.config.php');
					break;

			}
			$this->_authAdapter = $adapterClass;
		}
		return $this->_authAdapter;
	}
}