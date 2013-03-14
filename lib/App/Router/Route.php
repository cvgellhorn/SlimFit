<?php

/**
 * Original Version:
 * 
 * @author Rob Apodaca <rob.apodaca@gmail.com>
 * @copyright Copyright (c) 2009, Rob Apodaca
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link http://robap.github.com/php-router/
 * 
 * 
 * Modified Version:
 * 
 * @author cvgellhorn
 */
class App_Router_Route
{
	/**
	 * The Route path consisting of route elements
	 * @var string
	 */
	private $path;

	/**
	 * The name of the class that this route maps to
	 * @var string
	 */
	private $class;

	/**
	 * The name of the class method that this route maps to
	 * @var string
	 */
	private $method;

	/**
	 * Stores any set dynamic elements
	 * @var array 
	 */
	private $dynamicElements = array();

	/**
	 * Stores any arguments found when mapping
	 * @var array 
	 */
	private $mapArguments = array();

	/**
	 * Class Constructor
	 * @param string $path optional
	 */
	public function __construct($path = null)
	{
		if (null !== $path)
			$this->setPath($path);
	}

	/**
	 * Set the route path
	 * @param string $path
	 * @return Route
	 */
	public function setPath($path)
	{
		$this->path = $path;
		return $this;
	}

	/**
	 * Get the route path
	 * @return string
	 * @access public
	 */
	public function getPath()
	{
		return $this->path;
	}

	/**
	 * Set the map class name
	 * @param string $class
	 * @return Route
	 */
	public function setMapClass($class)
	{
		$this->class = $class;
		return $this;
	}

	/**
	 * Get the map class name
	 * @return string
	 * @access public
	 */
	public function getMapClass()
	{
		return $this->class;
	}

	/**
	 * Sets the map method name
	 * @param string $method
	 * @return Route
	 */
	public function setMapMethod($method)
	{
		$this->method = $method;
		return $this;
	}

	/**
	 * Gets the currently set map method
	 * @return string
	 */
	public function getMapMethod()
	{
		return $this->method;
	}

	/**
	 * Adds a dynamic element to the Route
	 * @param string $key
	 * @param string $value
	 * @return Route
	 */
	public function addDynamicElement($key, $value)
	{
		$this->dynamicElements[$key] = $value;
		return $this;
	}

	/**
	 * Get the dynamic elements array
	 * @return array
	 */
	public function getDynamicElements()
	{
		return $this->dynamicElements;
	}

	/**
	 * Adds a found argument to the _mapArguments array
	 * @param string $key
	 * @param string $value
	 * @return void
	 */
	private function addMapArguments($key, $value)
	{
		$this->mapArguments[$key] = $value;
	}

	/**
	 * Gets the _mapArguments array
	 * @return array
	 */
	public function getMapArguments()
	{
		return $this->mapArguments;
	}

	/**
	 * Attempt to match this route to a supplied path
	 * @param string $requestUri Request uri
	 * @return boolean
	 */
	public function matchMap($requestUri)
	{
		$foundDynamicClass = null;
		$foundDynamicMethod = null;
		$foundDynamicArgs = array();

		//-- Ignore query parameters during matching
		$parsed = parse_url($requestUri);
		$pathToMatch = $parsed['path'];

		//-- The process of matching is easier if there are no preceding slashes
		$tempThisPath = preg_replace('/^\//', '', $this->path);
		$tempPathToMatch = preg_replace('/^\//', '', $pathToMatch);

		//-- Get the path elements used for matching later
		$thisPathElements = explode('/', $tempThisPath);
		$matchPathElements = explode('/', $tempPathToMatch);
		
		//-- If the number of elements in each path is not the same, there is no
		// way this could be it.
		if (count($thisPathElements) !== count($matchPathElements))
			return false;

		//-- Construct a path string that will be used for matching
		$possibleMatchString = '';
		foreach ($thisPathElements as $i => $thisPathElement) {
			//-- ':'s are never allowed at the beginning of the path element
			if (preg_match('/^:/', $matchPathElements[$i])) {
				return false;
			}

			//-- This element may simply be static, if so the direct comparison
			// will discover it.
			if ($thisPathElement === $matchPathElements[$i]) {
				$possibleMatchString .= "/{$matchPathElements[$i]}";
				continue;
			}

			//-- Consult the dynamic array for help in matching
			if (true === isset($this->dynamicElements[$thisPathElement])) {
				//-- The dynamic array either contains a key like ':id' or a
				// regular expression. In the case of a key, the key matches
				// anything
				if ($this->dynamicElements[$thisPathElement] === $thisPathElement) {
					$possibleMatchString .= "/{$matchPathElements[$i]}";

					//-- The class and/or method may be getting set dynamically. If so
					// extract them and set them
					if (':class' === $thisPathElement && null === $this->getMapClass()) {
						$foundDynamicClass = $matchPathElements[$i];
					} else if (':method' === $thisPathElement && null === $this->getMapMethod()) {
						$foundDynamicMethod = $matchPathElements[$i];
					} else if (':class' !== $thisPathElement && ':method' !== $thisPathElement) {
						$foundDynamicArgs[$thisPathElement] = $matchPathElements[$i];
					}

					continue;
				}

				//-- Attempt a regular expression match
				$regexp = '/' . $this->dynamicElements[$thisPathElement] . '/';
				if (preg_match($regexp, $matchPathElements[$i]) > 0) {
					//-- The class and/or method may be getting set dynamically. If so
					// extract them and set them
					if (':class' === $thisPathElement && null === $this->getMapClass()) {
						$foundDynamicClass = $matchPathElements[$i];
					} else if (':method' === $thisPathElement && null === $this->getMapMethod()) {
						$foundDynamicMethod = $matchPathElements[$i];
					} else if (':class' !== $thisPathElement && ':method' !== $thisPathElement) {
						$foundDynamicArgs[$thisPathElement] = $matchPathElements[$i];
					}

					$possibleMatchString .= "/{$matchPathElements[$i]}";
					continue;
				}
			}

			//-- In order for a full match to succeed, all iterations must match.
			// Because we are continuing with the next loop if any conditions
			// above are met, if this point is reached, this route cannot be
			// a match.
			return false;
		}

		//-- Do the final comparison and return the result
		if ($possibleMatchString === $pathToMatch) {
			if (null !== $foundDynamicClass)
				$this->setMapClass($foundDynamicClass);

			if (null !== $foundDynamicMethod)
				$this->setMapMethod($foundDynamicMethod);

			foreach ($foundDynamicArgs as $key => $found_dynamic_arg) {
				$this->addMapArguments($key, $found_dynamic_arg);
			}
		}

		return ($possibleMatchString === $pathToMatch);
	}
}