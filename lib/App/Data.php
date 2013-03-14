<?php

/**
 * App_Data (global data functions / raw data object)
 *
 * @author cvgellhorn
 */
class App_Data extends App_Model
{
	/**
	 * Used method for serialize data to json
	 */
	public function toJson()
	{
		return self::arrayToJson($this->_data);
	}
	
	/*
	 * Used for printing object data by echo
	 */
	public function __toString()
	{
		return $this->toJson();
	}
	
	/**
	 * Format given object to array
	 *
	 * @param mixed $object Object
	 * @return array Data array
	 */
	public static function toArray($object)
	{
		$data = array();
		
		//-- Generate DB format strip [_]
		foreach(get_object_vars($object) as $key => $value)
			if(!is_object($value) && count($value) > 0)
				$data[trim($key, '_')] = $value;
		
		return $data;
	}
	
	/**
	 * Format given object to json
	 *
	 * @param mixed $object Object
	 * @return String Json data
	 */
	public static function objectToJson($object)
	{
		$values = array();
		foreach(get_object_vars($object) as $key => $value) {
			if(is_object($value)) {
				switch(get_class($value)) {
					case 'Zend_Date':
						$value = $value->getIso();
						break;
					default:
				}
			}
			//-- Generate camel case name
			$property = implode('', array_map('ucfirst', explode('_', $key)));
			$values[] = Zend_Json::encode($property).':'.Zend_Json::encode($value);
		}
		return '{'.implode(',', $values).'}';
	}
	
	/**
	 * Format given array to json
	 *
	 * @param array $array Data array
	 * @return String Json data
	 */
	public static function arrayToJson($array)
	{
		return '{'.implode(',', self::arrayToJsonRec($array)).'}';
	}
	
	/**
	 * Format given array to json [recursive]
	 *
	 * @param array $array Data array
	 * @return String Json data
	 */
	public static function arrayToJsonRec($data)
	{
		$values = array();
		foreach($data as $key => $value) {
			if(is_array($value))
				$value = self::arrayToJsonRec($value);
			
			//-- Generate camel case name
			$property = implode('', array_map('ucfirst', explode('_', $key)));
			$values[] = Zend_Json::encode($property).':'.Zend_Json::encode($value);
		}
		return $values;
	}
	
	/**
	 * Format given array to frontend ucfirst type
	 *
	 * @param array $array Data array
	 * @return String Json data
	 */
	public static function arrayToFrontend($data)
	{
		$values = array();
		foreach($data as $key => $value) {
			if(is_array($value))
				$value = self::arrayToFrontend($value);
			
			//-- Generate camel case name
			$property = implode('', array_map('ucfirst', explode('_', $key)));
			$values[$property] = $value;
		}
		return $values;
	}
}