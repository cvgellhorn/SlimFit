<?php namespace SlimFit;

use SlimFit\Exception;

/**
 * Response
 *
 * @author cvgellhorn
 */
class Response
{
	/**
	 * @var Response Instance implementation
	 */
	private static $_instance = null;
	
	/**
     * Array of headers. Each header is an array with keys 'name' and 'value'
     * @var array
     */
	protected $_headers = [];
	
	/**
     * HTTP response code to use in headers
     * @var int
     */
    protected $_httpResponseCode = 200;

	/**
	 * Single pattern implementation
	 * 
	 * @return Response
	 */
	public static function load()
	{
		if (null === self::$_instance) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}
	
	/**
	 * Private construct cause single pattern implementation
	 */
	private function __construct()
	{}
	
	/**
	 * Set header
	 * 
	 * @param string $name Header name
	 * @param string $val Header value
	 * @param bool $replace Replace another already header
	 * @return Response
	 */
	public function setHeader($name, $val, $replace = false)
	{
		if ($replace) {
            foreach ($this->_headers as $key => $header) {
                if ($name == $header['name']) {
                    unset($this->_headers[$key]);
                }
            }
        }
		
		$this->_headers[] = [
            'name'    => $name,
            'value'   => $val,
            'replace' => $replace
        ];

        return $this;
	}
	
	/**
	 * Set http response code
	 * 
	 * @param int $code Response code
	 * @return Response
	 * @throws Exception
	 */
	public function setHttpResponseCode($code)
	{
		if (!is_int($code) || (100 > $code) || (599 < $code)) {
            throw new Exception('Invalid HTTP response code');
        }

        if ((300 <= $code) && (307 >= $code)) {
            $this->_isRedirect = true;
        } else {
            $this->_isRedirect = false;
        }

        $this->_httpResponseCode = $code;
        return $this;
    }
	
	/**
	 * Send headers
	 * 
	 * @return Response
	 */
	public function sendHeaders()
	{
		// Only check if we can send headers if we have headers to send
        if (count($this->_headers) || (200 != $this->_httpResponseCode)) {
            //$this->canSendHeaders(true);
        } elseif (200 == $this->_httpResponseCode) {
            // Haven't changed the response code, and we have no headers
            return $this;
        }
		
		$httpCodeSent = false;
		foreach ($this->_headers as $header) {
            if (!$httpCodeSent && $this->_httpResponseCode) {
                header($header['name'] . ': ' . $header['value'], $header['replace'], $this->_httpResponseCode);
                $httpCodeSent = true;
            } else {
                header($header['name'] . ': ' . $header['value'], $header['replace']);
            }
        }

        if (!$httpCodeSent) {
            header('HTTP/1.1 ' . $this->_httpResponseCode);
            $httpCodeSent = true;
        }
		
		return $this;
	}
	
	/**
	 * Redirect to another uri
	 * 
	 * @param string $uri Uri to redirect
	 * @param int $code Response code
	 * @return Response
	 */
	public function redirect($uri, $code = 302)
	{
		//$this->canSendHeaders(true);
        $this->setHeader('Location', $uri, true)
             ->setHttpResponseCode($code)
			 ->sendHeaders();

        return $this;
	}
}