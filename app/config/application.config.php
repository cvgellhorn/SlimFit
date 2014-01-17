<?php

//-- Live Settings
return array(
	'app_path'		=> APP_PATH,
	'auth'			=> false,
	'base_path'		=> '/skelappton/',	// No base path == /
	'template'		=> 'default',
	'db'            => array(
		'host'      => 'localhost',
		'port'      => 3306,
		'user'      => 'root',
		'password'  => 'isa99#',
		'database'  => 'skelappton'
	),
	'auth'          => array(
		'adapter'   => 'db',
		'db_table'  => 'user'
	)
);