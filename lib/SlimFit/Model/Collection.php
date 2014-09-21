<?php

/**
 * Main data model
 *
 * @author cvgellhorn
 */
class SF_Model_Collection implements Iterator, ArrayAccess
{
	/**
	 * @var data to return
	 */
	protected $_data = array();
	
	/**
	 * Add new object to collection
	 */
	public function add(SF_Model $object)
	{
		$this->_data[] = $object;
	}
	
	/**
	 * Get data from $this object
	 */
	public function getData()
	{
		return $this->_data;
	}
	
	/**
	 * Set data to $this object
	 * 
	 * @param array $data Data array
	 */
	public function setData($data)
	{
		$this->_data = $data;
	}
	
	/**
	 * Count data elements
	 *
	 * @return int Elements count
	 */
	public function count()
	{
		return count($this->_data);
	}
	
	#region Iterator Methoden
	/**
	 * Implementation of Iterator::rewind()
	 *
	 * @link http://www.php.net/manual/en/iterator.rewind.php
	 */
	public function rewind()
	{
		return reset($this->_data);
	}
	
	/**
	 * Implementation of Iterator::current()
	 *
	 * @link http://www.php.net/manual/en/iterator.current.php
	 */
	public function current()
	{
		return current($this->_data);
	}
	
	/**
	 * Implementation of Iterator::key()
	 *
	 * @link http://www.php.net/manual/en/iterator.key.php
	 */
	public function key()
	{
		return key($this->_data);
	}
	
	/**
	 * Implementation of Iterator::next()
	 *
	 * @link http://www.php.net/manual/en/iterator.next.php
	 */
	public function next()
	{
		return next($this->_data);
	}
	
	/**
	 * Implementation of Iterator::valid()
	 *
	 * @link http://www.php.net/manual/en/iterator.valid.php
	 */
	public function valid()
	{
		return key($this->_data) !== null;
	}
	#endregion
	
	#region ArrayAccess Methoden
	/**
     * Implementation of ArrayAccess::offsetSet()
     *
     * @link http://www.php.net/manual/en/arrayaccess.offsetset.php
     * @param string $offset
     * @param mixed $value
     */
	public function offsetSet($offset, $value)
	{
		$this->_data[$offset] = $value;
	}
	
	/**
     * Implementation of ArrayAccess::offsetExists()
     *
     * @link http://www.php.net/manual/en/arrayaccess.offsetexists.php
     * @param string $offset
     * @return boolean
     */
	public function offsetExists($offset)
	{
		return isset($this->_data[$offset]);
	}
	
	/**
     * Implementation of ArrayAccess::offsetUnset()
     *
     * @link http://www.php.net/manual/en/arrayaccess.offsetunset.php
     * @param string $offset
     */
	public function offsetUnset($offset)
	{
		unset($this->_data[$offset]);
	}
	
	/**
     * Implementation of ArrayAccess::offsetGet()
     *
     * @link http://www.php.net/manual/en/arrayaccess.offsetget.php
     * @param string $offset
     * @return mixed
     */
	public function offsetGet($offset)
	{
		return isset($this->_data[$offset]) ? $this->_data[$offset] : null;
	}
	#endegion
}