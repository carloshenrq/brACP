<?php
// Defines the default exception handler for this app.
set_exception_handler(function($unhandledEx) {
	echo $unhandledEx->getMessage();
	exit;
});

// Defines the default error handler for this app.
set_error_handler(function($errno , $errstr, $errfile, $errline, $errcontext) {
    // Throws an error exception with all especifing data from the error.
    throw new AppException($errstr, $errno, 1, $errfile, $errline);
}, E_ALL);

// Defines the autoloader class for this app.
spl_autoload_register(function($className) {
    // Includes the class file on the code.

	if(!preg_match('/^Smarty/i', $className))
    	include_once (__DIR__ . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $className) . '.php');
}, true);


