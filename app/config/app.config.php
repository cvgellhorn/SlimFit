<?php

/**
 * base_path: No base path == /
 */

// Live settings
$production = [
	'base_path'		=> '/slimfit/',
	'template'		=> 'default',
	'db'            => [
		'host'      => 'localhost',
		'port'      => 3306,
		'user'      => 'root',
		'password'  => 'isa99#',
		'database'  => 'skelappton'
	],
	'auth'          => [
		'adapter'   => 'db',
		'db_table'  => 'user'
	]
];

// Development settings
$development = [
	'db' => [
		'database'  => 'slimfit_dev'
	]
];


return [
	'production'    => $production,
	'development'   => $development
];