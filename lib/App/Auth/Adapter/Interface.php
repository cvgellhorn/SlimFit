<?php

/**
 * Authentication adapter interface
 *
 * @author cvgellhorn
 */
interface App_Auth_Adapter_Interface
{
	/**
	 * Performs an login attempt
	 */
	public function login();
	
	/**
	 * Performs an logout attempt
	 */
	public function logout();
}