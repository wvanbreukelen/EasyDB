<?php

namespace EasyDB;

use EasyDB\Query\Builder;
use EasyDB\Query\Compiler;

class DB
{

	private static $instance;

	private $connection;
	private $compiler;

	public function __construct(Connection $connection, Compiler $compiler)
	{
		$this->setSelfInstance($this);
		$this->connection = $connection;
		$this->compiler = $compiler;
	}

	public function setSelfInstance($instance)
	{
		self::$instance = $instance;
	}

	public function getConnection()
	{
		return $this->connection;
	}

	public function getCompiler()
	{
		return $this->compiler;
	}

	protected function getBuilderInstance($table)
	{
		return new Builder($this->connection, $this->compiler, $table);
	}

	public static function table($table)
	{
		return self::$instance->getBuilderInstance($table);
	}
}