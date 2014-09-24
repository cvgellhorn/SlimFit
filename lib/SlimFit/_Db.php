<?php

/**
 * Main Database class
 *
 * @author cvgellhorn
 */
class SF_Db
{
	/**
	 * Instance implementation
	 *
	 * @var SF_Db
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
	private $_pdo;

	/**
	 * The driver level statement PDO
	 *
	 * @var PDOStatement
	 */
	protected $_stmt;

	/**
	 * Single pattern implementation
	 *
	 * @return SF_Db
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
	 * @return SF_Db
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
				throw new SF_Exception('pdo_mysql extension is not installed');
			}

			$config = SF_Ini::get('db');
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
			throw new SF_Exception($e->getMessage(), $e->getCode());
		}
	}

	/**
	 * Prepare SQL statement for executing
	 *
	 * @param string $sql SQL statement
	 * @return SF_Db
	 * @throws SF_Exception
	 */
	private function _prepare($sql)
	{
		$this->_stmt = $this->_pdo->prepare($sql);
		return $this;
	}

	/**
	 * Bind SQL query params to PDO statement object
	 *
	 * @param array $data SQL query params
	 * @return SF_Db
	 */
	private function _bindParams($data)
	{
		$count = count($data);
		for ($i = 0; $i < $count; $i++) {
			$this->_stmt->bindParam($i + 1, $data[$i]);
		}

		return $this;
	}

	/**
	 * Execute SQL statement
	 *
	 * @return PDOStatement
	 * @throws SF_Exception
	 */
	private function _execute()
	{
		try {
			$this->_stmt->execute();
		} catch (PDOException $e) {
			throw new SF_Exception('PDO Mysql execution error: ' . $e->getMessage(), $e->getCode());
		}

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
	 * Return given value with quotes
	 *
	 * @param string $val Value
	 * @return string Value with quotes
	 */
	public function quote($val)
	{
		return "'$val'";
	}

	/**
	 * Return given value with backticks
	 *
	 * @param string $val Value
	 * @return string Value with backticks
	 */
	public function btick($val)
	{
		return "`$val`";
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
	 * Fetch row by SQL statement
	 *
	 * @param string $sql SQL statement
	 * @return array SQL result
	 */
	public function fetchRow($sql)
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

	/**
	 * Executes an SQL statement
	 *
	 * @param string $sql SQL statement
	 * @return array|bool|mixed|null SQL result
	 * @throws SF_Exception
	 */
	public function query($sql)
	{
		try {
			/**
			 * @var $result PDOStatement
			 */
			$result = $this->_pdo->query($sql);
		} catch (PDOException $e) {
			throw new SF_Exception('PDO Mysql statement error: ' . $e->getMessage(), $e->getCode());
		}

		$columnCount = $result->columnCount();
		$rowCount = $result->rowCount();

		// If statment is as SELECT statement
		if ($columnCount > 0) {
			// Equal to fetchOne
			if ($columnCount === 1 && $rowCount === 1) {
				$res = $result->fetch(PDO::FETCH_NUM);
				return isset($res[0]) ? $res[0] : null;

			// Equal to fetchRow
			} else if ($columnCount > 1 && $rowCount === 1) {
				return $result->fetch(PDO::FETCH_ASSOC);

			// Equal to fetchAll
			} else {
				return $result->fetchAll(PDO::FETCH_ASSOC);
			}
		} else {
			return true;
		}
	}

	/**
	 * Insert given data into database
	 *
	 * @param strin $table DB table name
	 * @param array $data Data to insert
	 * @return int Last insert ID
	 */
	public function insert($table, $data)
	{
		$keys = array();
		$values = array();

		foreach ($data as $key => $val) {
			$keys[] = $this->btick($key);
			if ($val instanceof SF_Db_Expr) {
				$values[] = $val;
				unset($data[$key]);
			} else {
				$values[] = '?';
			}
		}

		$query = 'INSERT INTO ' . $this->btick($table)
				. ' (' . implode(', ', $keys) . ')'
				. ' VALUES (' . implode(', ', $values) . ')';

		$this->_prepare($query)->_bindParams(array_values($data))->_execute();
		return $this->_pdo->lastInsertId();
	}

	/**
	 * Do a multi insert
	 *
	 * TODO: try binding for multiple rows
	 */
	public function multiInsert()
	{}

	/**
	 * ON DUPLICATE KEY UPDATE
	 *
	 * TODO: build on duplicate key update method
	 */
	public function save($table, $data)
	{}

	/**
	 * Update data by given condition
	 *
	 * @param string $table DB table name
	 * @param array $data Data to update
	 * @param array $where Update condition
	 */
	public function update($table, $data, $where = array())
	{
		$query = 'UPDATE ' . $this->btick($table) . ' SET ';

		$par = array();
		foreach ($data as $key => $val) {
			if ($val instanceof SF_Db_Expr) {
				$par[] = $this->btick($key) . ' = ' . $val;
				unset($data[$key]);
			} else {
				$par[] = $this->btick($key) . ' = ?';
			}
		}
		$query .= implode(', ', $par);

		if (!empty($where)) {
			$query .= ' WHERE ' . implode(' AND ', array_keys($where));
		}

		$params = array_merge(
			array_values($data),
			array_values($where)
		);

		$this->_prepare($query)->_bindParams($params)->_execute();
	}

	/**
	 * Delete from database table
	 *
	 * @param string $table DB table name
	 * @param array $where Delete condition
	 */
	public function delete($table, $where = array())
	{
		$query = 'DELETE FROM ' . $this->btick($table);
		if (!empty($where)) {
			$query .= ' WHERE ' . implode(' AND ', array_keys($where));
		}

		$this->_prepare($query);
		if (!empty($where)) {
			$this->_bindParams(array_values($where));
		}
		$this->_execute();
	}

	/**
	 * Truncate database table
	 *
	 * @param string $table DB table name
	 */
	public function truncate($table)
	{
		$this->_prepare('TRUNCATE TABLE ' . $this->btick($table))->_execute();
	}

	/**
	 * Drop database table
	 *
	 * @param string $table DB table name
	 */
	public function drop($table)
	{
		$this->_prepare('DROP TABLE ' . $this->btick($table))->_execute();
	}
}