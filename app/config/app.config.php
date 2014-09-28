<?php

/**
 * Default settings:
 *
 * base_path: No base path == /
 */

// Live settings
$production = [
	'app_name'      => 'SlimFit',
	'base_path'		=> '/slimfit/',
	'layout'		=> 'default',
	'db'            => [
		'host'      => 'localhost',
		'user'      => 'root',
		'password'  => 'live123#',
		'database'  => 'slimfit'
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