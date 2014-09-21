<?php

/**
 * Default settings:
 *
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
		'password'  => 'live123#',
		'database'  => 'slimfit'
	],
	'auth'          => [
		'adapter'   => 'db',
		'db_table'  => 'user'
	]
];

// Development settings
$development = [
	'db' => [
		'database'  => 'slimfit_dev',
		'user'      => 'root',
		'password'  => 'dev123#'
	]
];


// Current environment inherit config from live environment
return [
	Config::ENV_LIVE => $production,
	Config::ENV_DEV  => $development
];