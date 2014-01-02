<?php

define('SPACE', " ");

require('ConnectionInterface.php');
require('Connection.php');
require('DB.php');
require('Query/Compiler.php');
require('Query/Builder.php');

use EasyDB\DB;
use EasyDB\Connection;

new DB(new Connection(new PDO('mysql:host=127.0.0.1;dbname=easydb', 'root', '')), new EasyDB\Query\Compiler);

// DB::table('users')->select()->where('username', 'testme80')->build();
// DB::table('users')->insert()->values(array('userID' => '', 'username' => 'testme80', 'password' => 'wokkel4', 'active' => ''))->build();


DB::table('users')->update()->values(array('username' => 'gayly'))->where('username', 'testme80')->build();