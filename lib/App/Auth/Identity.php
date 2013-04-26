<?php

/**
 * Auth Identity object
 *
 * @author cvgellhorn
 */
class App_Auth_Identity extends App_Model
{
	/**
	 * Get the current user id
	 */
	public function getId()
	{
		return $this->getData('id');
	}
	
	/**
	 * Get the current username
	 */
	public function getUsername()
	{
		return $this->getData('username');
	}
	
	/**
	 * Get the current user email
	 */
	public function getEmail()
	{
		return $this->getData('email');
	}
	
	/**
	 * Get the current user firstname
	 */
	public function getFirstname()
	{
		return $this->getData('firstname');
	}
	
	/**
	 * Get the current user lastname
	 */
	public function getLastname()
	{
		return $this->getData('lastname');
	}
}