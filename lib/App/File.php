<?php

/**
 * App file manager
 *
 * @author cvgellhorn
 */
class App_File
{
	/**
	 * @var Allowed file suffixes
	 */
	const SUFFIX_PNG	= 'png';
	const SUFFIX_JPG	= 'jpg';
	const SUFFIX_JPEG	= 'jpeg';
	const SUFFIX_GIF	= 'gif';
	const SUFFIX_TXT	= 'txt';
	const SUFFIX_PDF	= 'pdf';
	
	/**
	 * @var Storage units
	 */
	const UNIT_KB = 1024;		// kilobyte
	const UNIT_MB = 1048576;	// megabyte
	const UNIT_GB = 1073741824; // gigabyte
	
	/**
	 * @var string File name
	 */
	private $_file = null;
	
	/**
	 * Check allowed file suffixes
	 *
	 * @param String $suffix Filename or file suffix
	 * @return bool Allowed?
	 */
	public static function isAllowed($suffix)
	{
		if(strrpos($suffix, '.')) {
			$suffix = pathinfo($suffix, PATHINFO_EXTENSION);
		}

		$suffix = strtolower($suffix);
		switch($suffix) {
			case self::SUFFIX_PNG:
				return true;
			case self::SUFFIX_JPG:
				return true;
			case self::SUFFIX_JPEG:
				return true;
			case self::SUFFIX_GIF:
				return true;
			case self::SUFFIX_TXT:
				return true;
			case self::SUFFIX_PDF:
				return true;
		}
		return false;
	}
	
	/**
	 * Get file size
	 *
	 * @param String $file file basename
	 * @param int $unit Storage unit
	 * @return float File size
	 */
	public static function getSize($file, $unit = self::UNIT_MB)
	{
		if (file_exists($file)) {
			return round(filesize($file) / $unit, 2);
		} else {
			return 0;
		}
	}
	
	/**
	 * Create directory if not exists
	 *
	 * @param String $dir Directory name
	 * @param bool $rec Recursive
	 */
	public static function createDirIfNotExists($dir, $rec = false)
	{
		if (!is_dir($dir)) {
			mkdir($dir, 0777, $rec);
		}
	}
	
	/**
	 * Main upload file method (incl. logging)
	 * 
	 * @param String $fileData Temp file name
	 * @param String $path New file path
	 * @throws App_Exception
	 */
	public static function upload($fileData, $path)
	{
		try {
			$tempFile = $fileData['tmp_name'];
			$fileName = $fileData['name'];
			$fileSize = $fileData['size'];
			$oldImagePath = $fileData['oldImagePath'];
			$filepath = uniqid() . '_' . $fileName;
			
			if(self::isAllowed($fileName)) {
				move_uploaded_file($tempFile, $filepath);
				//unlink($oldImagePath);
				App::log('File: '.$filepath.' successfully uploaded', Zend_Log::INFO);
			} else {
				throw new App_Exception('File format/extension not valid', 5007);
			}
		} catch (Exception $e) {
			throw new App_Exception($e, 5005);
		}
	}
	
	/**
	 * Main delete file method (incl. logging)
	 *
	 * @param String $file Path with filename
	 * @param bool $allSuffixes Delete all suffixes?
	 * @throws App_Exception
	 */
	public static function delete($file, $allSuffixes = false)
	{
		try {
			if($allSuffixes) {
				$dotPos = strrpos($file, '.');
				if(!$dotPos) {
					$mask = $file . '.*';
				} else {
					$mask = substr($file, 0, $dotPos) . '.*';
				}

				array_map('unlink', glob($mask));
				App::log('File with all suffixes: ' . $file . ' successfully deleted', Zend_Log::INFO);
			} else {
				unlink($file);
				App::log('File: ' . $file . ' successfully deleted', Zend_Log::INFO);
			}
		} catch (Exception $e) {
			throw new App_Exception($e, 5006);
		}
	}
	
	/**
	 * Create schedule log tmp directory and move file
	 *
	 * @param String $logFile File basename
	 */
	public static function createLogTmp($logFile)
	{
		if (file_exists($logFile)) {
			$pathInfo = pathinfo($logFile);
			$fileName = $pathInfo['filename'];
			$scheduleDir = $pathInfo['dirname'] . DS . $fileName;
			
			self::createDirIfNotExists($scheduleDir);
			rename($logFile, $scheduleDir . DS . $fileName . '_' . time() . '.log');
		}
	}
	
	public static function rename()
	{}
	
	/**
	 * App_File constructor
	 *
	 * @param string $fileName Filename
	 * @param bool $newFile Create new file?
	 * @throws App_Exception
	 */
	public function __construct($fileName = 'default.txt', $newFile = false)
	{
		if(self::isAllowed($fileName)) {
			$fileDir = App::getBaseDir('data' . DS . 'files');
			self::createDirIfNotExists($fileDir);
			
			$file = $fileDir . DS . $fileName;
			if (!file_exists($file)) {
				file_put_contents($file, '');
				chmod($file, 0777);
			} else {
				if($newFile) {
					$pathInfo = pathinfo($file);
					$file = $pathInfo['dirname'] 
						. '/' . $pathInfo['filename'] 
						. '_' . time() 
						. '.' . $pathInfo['extension'];

					file_put_contents($file, '');
					chmod($file, 0777);
				}
			}
			$this->_file = $file;
		} else {
			throw new App_Exception('File format/extension not valid', 5007);
		}
	}
	
	/**
	 * Get current file
	 *
	 * @return string File basename incl. dir path
	 */
	public function getFile()
	{
		return $this->_file;
	}
	
	/**
	 * Get file size
	 *
	 * @return float File size
	 */
	public function getFileSize()
	{
		return self::getSize($this->_file);
	}
	
	/**
	 * Write data to file
	 *
	 * @param mixed $value Data to write
	 * @param bool $newLine Add new line
	 * @param bool $append Append data
	 */
	public function write($value, $newLine = false, $append = true)
	{
		$value = ($newLine) ? PHP_EOL . $value : $value;
		if($append) {
			file_put_contents($this->_file, $value, FILE_APPEND | LOCK_EX);
		} else {
			file_put_contents($this->_file, $value, LOCK_EX);
		}
	}
	
	/**
	 * Overwrite file
	 *
	 * @param mixed $value Data to write
	 * @param bool $newLine Add new line
	 */
	public function overWrite($value, $newLine = false)
	{
		$this->write($value, $newLine, false);
	}
}