<?php

/**
 * Main Request object
 * 
 * @author cvgellhorn
 */
class App_Request
{
	/**
	 * Instance implementation
	 *
	 * @var App_Request
	 */
	private static $_instance = null;

	/**
	 * Request options
	 */
	private $_uri;
	private $_routerUri;
	private $_controller;
	private $_action;

	/**
	 * @var array Instance parameters
	 */
	private $_params = array();

	/**
	 * @var bool Is internal request
	 */
	private $_isInternal = false;

	/**
	 * Single pattern implementation
	 * 
	 * @return App_Request
	 */
	public static function getInstance()
	{
		if (null === self::$_instance)
			self::$_instance = new self();

		return self::$_instance;
	}
	
	/**
	 * Set requested uri
	 */
	private function __construct()
	{
		$this->_uri = urldecode($_SERVER['REQUEST_URI']);
	}
	
	/**
	 * Private clone cause single pattern implementation
	 */
	private function __clone() {}
	
	/**
	 * Get the current requested uri
	 * 
	 * @return String Requested uri
	 */
	public function getUri()
	{
		return $this->_uri;
	}
	
	/**
	 * Set new request uri
	 * 
	 * @param string $uri New request uri
	 * @return self
	 */
	public function setUri($uri)
	{
		$this->_uri = $uri;
		return $this;
	}
	
	/**
	 * Get the current requested uri
	 * 
	 * @return String Requested uri
	 */
	public function getRouterUri()
	{
		return $this->_routerUri;
	}

	/**
	 * Get the current controller
	 * 
	 * @return String Current controller name
	 */
	public function getControllerName()
	{
		return $this->_controller;
	}
	
	/**
	 * Set the current controller name
	 * 
	 * @param string $controller Controller name
	 * @return App_Request
	 */
	public function setControllerName($controller)
	{
		$this->_controller = $controller;
		return $this;
	}
	
	/**
	 * Get the current action
	 * 
	 * @return String Current action name
	 */
	public function getActionName()
	{
		return $this->_action;
	}
	
	/**
	 * Set the current action
	 * 
	 * @param string $name Action name
	 * @return App_Request
	 */
	public function setActionName($name)
	{
		$this->_action = $name;
		return $this;
	}
	
	/**
     * Set an action parameter
     *
     * A $value of null will unset the $key if it exists
     *
     * @param string $key
     * @param mixed $val
     * @return App_Request
     */
	public function setParam($key, $val)
	{
		$key = (string)$key;

        if ((null === $val) && isset($this->_params[$key])) {
            unset($this->_params[$key]);
        } elseif (null !== $val) {
            $this->_params[$key] = $val;
        }
		
		return $this;
	}
	
	/**
     * Get an action parameter
     *
     * @param string $key
     * @param mixed $default Default value to use if key not found
     * @return mixed
     */
	public function getParam($key, $default = null)
	{
        if (isset($this->_params[$key])) {
            return $this->_params[$key];
        } elseif (isset($_GET[$key])) {
            return $_GET[$key];
        } elseif (isset($_POST[$key])) {
            return $_POST[$key];
        }

        return $default;
	}
	
	/**
     * Get all action parameters
     *
     * @return array Request params
     */
	public function getParams()
	{
		return $this->_params;
	}
	
	/**
     * Unset all user parameters
     *
     * @return App_Request
     */
    public function clearParams()
    {
        $this->_params = array();
        return $this;
    }
	
	/**
     * Retrieve a member of the $_SERVER superglobal
     *
     * If no $key is passed, returns the entire $_SERVER array.
     *
     * @param string $key
     * @param mixed $default Default value to use if key not found
     * @return mixed Returns null if key does not exist
     */
	public function getServer($key = null, $default = null)
	{
		if (null === $key)
			return $_SERVER;

		return (isset($_SERVER[$key])) ? $_SERVER[$key] : $default;
	}
	
	/**
     * Retrieve a member of the $_GET superglobal
     *
     * If no $key is passed, returns the entire $_GET array.
     *
     * @param string $key
     * @param mixed $default Default value to use if key not found
     * @return mixed Returns null if key does not exist
     */
	public function getGet($key = null, $default = null)
	{
		if (null === $key)
			return $_GET;

		return (isset($_GET[$key])) ? $_GET[$key] : $default;
	}
	
	/**
     * Retrieve a member of the $_POST superglobal
     *
     * If no $key is passed, returns the entire $_POST array.
     *
     * @param string $key
     * @param mixed $default Default value to use if key not found
     * @return mixed Returns null if key does not exist
     */
	public function getPost($key = null, $default = null)
	{
		if (null === $key)
			return $_POST;

		return (isset($_POST[$key])) ? $_POST[$key] : $default;
	}
	
	/**
     * Return the value of the given HTTP header. Pass the header name as the
     * plain, HTTP-specified header name. Ex.: Ask for 'Accept' to get the
     * Accept header, 'Accept-Encoding' to get the Accept-Encoding header.
     *
     * @param string $header HTTP header name
     * @return string|false HTTP header value, or false if not found
     */
	public function getHeader($header)
	{
		// Try to get it from the $_SERVER array first
		$temp = 'HTTP_' . strtoupper(str_replace('-', '_', $header));
		if (isset($_SERVER[$temp]))
			return $_SERVER[$temp];

		// Seems to be the only way to get the Authorization header on Apache
		if (function_exists('apache_request_headers')) {
			$headers = apache_request_headers();
			if (isset($headers[$header]))
				return $headers[$header];

			$header = strtolower($header);
			foreach ($headers as $key => $value) {
				if (strtolower($key) == $header)
					return $value;
			}
		}

		return false;
    }
	
	/**
     * Return the method by which the request was made
     *
     * @return string
     */
	public function getMethod()
	{
		return $this->getServer('REQUEST_METHOD');
	}
	
	/**
	 * Was the request made by POST?
	 *
	 * @return boolean
	 */
	public function isPost()
	{
		return ('POST' == $this->getMethod()) ? true : false;
	}

	/**
	 * Was the request made by GET?
	 *
	 * @return boolean
	 */
	public function isGet()
	{
		return ('GET' == $this->getMethod()) ? true : false;
	}
	
	/**
     * Is the request a Javascript XMLHttpRequest?
     *
     * Should work with: 
	 * jQuery/Prototype/Script.aculo.us/Yahoo! UI Library/MochiKit
     *
     * @return boolean
     */
	public function isAjaxRequest()
	{
		return ($this->getHeader('X_REQUESTED_WITH') == 'XMLHttpRequest');
	}
	
	/**
	 * Set request is internal
	 */
	public function setIsInternal()
	{
		$this->_isInternal = true;
		return $this;
	}
	
	/**
	 * Get the current request state
	 */
	public function isInternal()
	{
		return $this->_isInternal;
	}
}