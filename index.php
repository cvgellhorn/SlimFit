<?php

// Define directory separator alias
defined('DS') || define('DS', DIRECTORY_SEPARATOR);

// Define application main directories
defined('BASE_DIR') || define('BASE_DIR', __DIR__ . DS);
defined('APP_DIR')  || define('APP_DIR', BASE_DIR . 'app');

// Define application environment
defined('APP_ENV') || define('APP_ENV',
	(getenv('APP_ENV') ? getenv('APP_ENV') : 'production'));

// Set include path
set_include_path(implode(PATH_SEPARATOR, [
	BASE_DIR . 'lib',
	get_include_path(),
]));

// Register autoloader
require_once('SlimFit/Autoloader.php');
\SlimFit\Autoloader::register();

// Run SlimFit application
\SlimFit\SF::run();