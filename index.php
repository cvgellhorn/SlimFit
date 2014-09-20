<?php

// Define directory separator alias
defined('DS') || define('DS', DIRECTORY_SEPARATOR);

// Define application main directories
defined('BASE_DIR') || define('BASE_DIR', __DIR__ . DS);
define('APP_DIR', BASE_DIR . 'app');

// Define application environment
defined('APP_ENV') || define('APP_ENV',
	(getenv('APP_ENV') ? getenv('APP_ENV') : 'production'));

// Set include path
set_include_path(implode(PATH_SEPARATOR, array(
	BASE_DIR . 'lib',
    get_include_path(),
)));

// Register autoloader
require_once('SF/Autoloader.php');
\SF\Autoloader::register();

// Run SlimFit application
SF::run();