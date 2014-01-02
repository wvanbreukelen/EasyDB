<?php

define('SPACE', " ");

require('ConnectionInterface.php');
require('Connection.php');
require('DB.php');
require('Query/Builder.php');

use EasyDB\DB;

new DB(new EasyDB\Connection);

DB::table('users')->select()->where('username', 'testme80')->build();