<?php namespace SlimFit;

use Exception;
use SlimFit\SF;

/**
 * Catch all the Orcs!
 * They will destroy you, if you let them. Don't let them win, you're the choosen one!
 * 
 * @author cvgellhorn
 */
class Orc extends Exception
{
	/**
	 * Constructor of exceptions class
	 * 
	 * @param string @message Exception message
	 * @param int @code Exception code
	 */
	public function __construct($message = null, $code = null) 
	{
		parent::__construct($message, $code);
		SF::log($this, SF::LOG_FILE_ERROR);
	}
}