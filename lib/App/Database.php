<?php

/**
 * Main Database class
 *
 * @author cvgellhorn
 */
class App_Database
{
	/**
	 * Instance implementation
	 *
	 * @var App_Database
	 */
	private static $_instance = null;

	/**
	 * Collection of active database connections
	 *
	 * @var array of App_Database
	 */
	private static $_connections = array();

	/**
	 * PDO Mysql object
	 *
	 * @var PDO
	 */
	private $_pdo = null;

	/**
	 * The driver level statement PDO
	 *
	 * @var PDOStatement
	 */
	protected $_stmt = null;

	/**
	 * Single pattern implementation
	 *
	 * @return App_Database
	 */
	public static function getInstance()
	{
		if (null === self::$_instance)
			self::$_instance = new self();

		return self::$_instance;
	}

	/**
	 * Create and get new database connection
	 *
	 * @param string $name Connection name
	 * @return App_Database
	 */
	public static function getConnection($name = null)
	{
		if (null === $name) {
			return new self();
		} else {
			if (!isset(self::$_connections[$name])) {
				self::$_connections[$name] = new self();
			}
			return self::$_connections[$name];
		}
	}

	/**
	 * Create DB object and connect to MySQL
	 */
	private function __construct($pdoType = 'mysql')
	{
		try {
			if (!extension_loaded('pdo_mysql')) {
				throw new App_Exception('pdo_mysql extension is not installed');
			}

			$config = App_Ini::get('db');
			$dsn = array(
				'host=' . $config['host'],
				'dbname=' . $config['database']
			);

			$this->_pdo = new PDO(
				$pdoType . ':' . implode(';', $dsn),
				$config['user'],
				$config['password']
			);

			// Always use exceptions
			$this->_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch (PDOException $e) {
			throw new App_Exception($e->getMessage(), $e->getCode());
		}
	}

	/**
	 * Prepare SQL statement for executing
	 *
	 * @param string $sql SQL statement
	 * @return PDOStatement
	 * @throws App_Exception
	 */
	private function _prepare($sql)
	{
		$this->_stmt = $this->_pdo->prepare($sql);

		if ($this->_stmt === false) {
			throw new App_Exception('PDO Mysql prepare error: ' . $this->_pdo->error, $this->_pdo->errno);
		}

		return $this;
	}

	/**
	 * Execute SQL statement
	 *
	 * @return PDOStatement
	 * @throws App_Exception
	 */
	private function _execute()
	{
		if (!$this->_stmt) {
			throw new App_Exception('PDO Mysql statement error: ' . $this->_pdo->error, $this->_pdo->errno);
		}

		$this->_stmt->execute();
		return $this->_stmt;
	}

	/**
	 * Get the current PDO object
	 *
	 * @return PDO
	 */
	public function getPDO()
	{
		return $this->_pdo;
	}

	/**
	 * Initiates a transaction
	 *
	 * @return bool TRUE on success or FALSE on failure
	 */
	public function beginTransaction()
	{
		return $this->_pdo->beginTransaction();
	}

	/**
	 * Commits a transaction
	 *
	 * @return bool TRUE on success or FALSE on failure
	 */
	public function commit()
	{
		return $this->_pdo->commit();
	}

	/**
	 * Rolls back a transaction
	 *
	 * @return bool TRUE on success or FALSE on failure
	 */
	public function rollBack()
	{
		return $this->_pdo->rollBack();
	}

	/**
	 * Checks if inside a transaction
	 *
	 * @return bool TRUE on success or FALSE on failure
	 */
	public function inTransaction()
	{
		return $this->_pdo->inTransaction();
	}

	/**
	 * Get last insert ID
	 *
	 * @return string Last insert ID
	 */
	public function lastInsertId()
	{
		return $this->_pdo->lastInsertId();
	}

	/**
	 * Fetch all data by SQL statement
	 *
	 * @param string $sql SQL statement
	 * @return array SQL result
	 */
	public function fetchAll($sql)
	{
		return $this->_prepare($sql)->_execute()->fetchAll(PDO::FETCH_ASSOC);
	}

	/**
	 * Fetch all data by SQL statement and merge by field
	 *
	 * @param string $sql SQL statement
	 * @param string $key Optional | array key
	 * @return array SQL result
	 */
	public function fetchAssoc($sql, $key = 'id')
	{
		// Raw result data
		$data = $this->_prepare($sql)->_execute()->fetchAll(PDO::FETCH_ASSOC);

		$result = array();
		if (!empty($data) && isset($data[0][$key])) {
			foreach ($data as $d) {
				$result[$d[$key]] = $d;
			}
		} else {
			$result = $data;
		}

		return $result;
	}

	/**
	 * Fetch column by SQL statement
	 *
	 * @param string $sql SQL statement
	 * @return array SQL result
	 */
	public function fetchColumn($sql)
	{
		return $this->_prepare($sql)->_execute()->fetch(PDO::FETCH_ASSOC);
	}

	/**
	 * Fetch single value by SQL statement
	 *
	 * @param string $sql SQL statement
	 * @return mixed Result value
	 */
	public function fetchOne($sql)
	{
		$result = $this->_prepare($sql)->_execute()->fetch(PDO::FETCH_NUM);
		return isset($result[0]) ? $result[0] : null;
	}

	public function query()
	{}

	public function insert($data, $table)
	{
		$query = 'INSERT INTO `' . $table . '` ';
		$query .= '(' . implode(', ', array_keys($data)) . ') ';
		$query .= 'VALUES (' . implode(', ', array_fill(0, count($data), '?')) . ')';

		App_Debug::dump($query);

		//$this->_execute();
		//return $this->_pdo->lastInsertId();
	}

	public function multiInsert($meta, $values)
	{
		$stmt = array();
		foreach ($values as $value) {
			$val = array();
			foreach ($value as $v) {
				$val[] = $v;
			}
			$stmt[] = '(' . implode(', ', $val) . ')';
		}

		$statement = implode(', ', $stmt);
	}

	public function update()
	{}

	public function delete()
	{}
}