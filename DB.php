<?php

namespace EasyDB;

use EasyDB\Query\Builder;

class DB
{

	private static $instance;

	private $connection;

	public function __construct(Connection $connection)
	{
		$this->setSelfInstance($this);
		$this->connection = $connection;
	}

	public function setSelfInstance($instance)
	{
		self::$instance = $instance;
	}

	public function getConnection()
	{
		return $this->connection;
	}

	protected function getBuilderInstance($table)
	{
		return new Builder($this->connection, $table);
	}

	public static function table($table)
	{
		return self::$instance->getBuilderInstance($table);
	}
}