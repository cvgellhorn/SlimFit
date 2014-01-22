<?php

/**
 * Main Database class
 *
 * @author cvgellhorn
 */
class App_Database
{
	/**
	 * Parameter bind types
	 */
	const BIND_TYPE_NAMED   = 'named';
	const BIND_TYPE_NUM     = 'num';

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
	 * @return App_Database
	 * @throws App_Exception
	 */
	private function _prepare($sql)
	{
		$this->_stmt = $this->_pdo->prepare($sql);

		if (false === $this->_stmt) {
			throw new App_Exception('PDO Mysql prepare error: ' . $this->_pdo->error, $this->_pdo->errno);
		}

		return $this;
	}

	/**
	 * Bind SQL query params to PDO statement object
	 *
	 * @param array $data SQL query params
	 * @param string $type Parameter bind type
	 * @return App_Database
	 */
	private function _bindParams($data, $type = self::BIND_TYPE_NAMED)
	{
		if ($type === self::BIND_TYPE_NAMED) {
			foreach ($data as $key => &$val) {
				if ($val instanceof App_Db_Expr) {
					$this->_stmt->bindParam(':' . $key, $val, PDO::PARAM_STMT);
				} else {
					$this->_stmt->bindParam(':' . $key, $val);
				}
			}
		} else if ($type === self::BIND_TYPE_NUM) {
			$count = count($data);
			for ($i = 0; $i < $count; $i++) {
				$this->_stmt->bindParam($i + 1, $data[$i]);
			}
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

	public function query()
	{}

	/**
	 * Insert given data into database
	 *
	 * @param strin $table DB table name
	 * @param array $data Data to insert
	 * @return int Last insert ID
	 */
	public function insert($table, $data)
	{
		$keys = array_keys($data);
		$query = 'INSERT INTO ' . $this->btick($table)
				. ' (' . implode(', ', $keys) . ')'
				. ' VALUES (:' . implode(', :', $keys) . ')';

		$this->_prepare($query)->_bindParams($data)->_execute();
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
			$par[] = $this->btick($key) . ' = ?';
		}
		$query .= implode(', ', $par);

		if (!empty($where)) {
			$query .= ' WHERE ' . implode(' AND ', array_keys($where));
		}

		$params = array_merge(
			array_values($data),
			array_values($where)
		);

		$this->_prepare($query)
			 ->_bindParams($params, self::BIND_TYPE_NUM)
			 ->_execute();
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
			$this->_bindParams(array_values($where), self::BIND_TYPE_NUM);
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

class App_Db_Expr
{
	public $key;

	public function __construct($key)
	{
		$this->key = $key;
	}

	public function __toString()
	{
		return $this->key;
	}
}