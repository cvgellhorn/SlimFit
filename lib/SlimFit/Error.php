<?php namespace SlimFit;

use Exception;
use SLimFit\SF;

/**
 * Main exception class
 * 
 * @author cvgellhorn
 */
class Error extends Exception
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