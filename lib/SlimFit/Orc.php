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
	 * Mordor
	 * 
	 * @param string @message Orcs mission
	 * @param int @code Orcs id
	 */
	public function __construct($message = null, $code = null) 
	{
		parent::__construct($message, $code);
		SF::log($this, SF::LOG_FILE_ERROR);
	}
}