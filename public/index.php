<?php

// Define directory separator
defined('DS') || define('DS', DIRECTORY_SEPARATOR);

// Define path to application directory
defined('APP_PATH') || define('APP_PATH',
              realpath(dirname(__FILE__) . DS . '..' . DS . 'app'));
 
// Define application environment
defined('APP_ENV') || define('APP_ENV',
              (getenv('APP_ENV') ? getenv('APP_ENV') : 'production'));

// Set include path
set_include_path(implode(PATH_SEPARATOR, array(
    dirname(dirname(__FILE__)) . '/lib',
    get_include_path(),
)));

// Include main SlimFit class
require_once 'SF.php';

// Run SlimFit application
SF::run();