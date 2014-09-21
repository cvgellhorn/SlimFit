<?php

/**
 * Database Expression
 */
class SF_Db_Expr
{
	/**
	 * @var string Database expression
	 */
	public $expr;

	/**
	 * Expression constructor
	 *
	 * @param string $expr Database expression
	 */
	public function __construct($expr)
	{
		$this->expr = $expr;
	}

	/**
	 * Magic to string method
	 *
	 * @return string Database expression
	 */
	public function __toString()
	{
		return $this->expr;
	}
}