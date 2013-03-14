<?php

/**
 * Main exception class
 * 
 * @author cvgellhorn
 */
class App_Exception extends Exception
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

		$logger = App::getLogger(App::LOG_FILE_ERROR);
		$logger->log($this, Zend_Log::ERR);
		
		/*if (APP_ENV != 'development') {
			$errMail = new App_Mail();
			$errMail->setMailSubject('App Error');
			$errMail->setMailTo('gellborn@gmail.com');
			$errMail->setMailBody('[Error Code: '.$code.'] '.$this);
			$errMail->sendMail();
		}*/
	}

	/**
	 * Getting errors in xmlHttpRequests
	 */
	public function toJson() 
	{
		if (APP_ENV != 'testing') {
			$result = new stdClass();
			$result->Message	= $this->getMessage();
			$result->Code		= $this->getCode();
			$result->File		= $this->getFile();
			$result->Line		= $this->getLine();
			$result->Trace		= $this->getTrace();
			
			return json_encode($result);
		} else {
			return json_encode('Message: '.$this->getMessage().'### Code: '.$this->getCode()
				.' ### File: '.$this->getFile().' ### Line: '.$this->getLine().' ### Trace: '.$this->getTrace());
		}
	}
}