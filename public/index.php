<?php

/**
 * Register the autoloader
 */
require __DIR__ . '/../bootstrap/Autoload.php';

/**
 * Register the error handler
 */
require __DIR__ . '/../bootstrap/Error.php';

/**
 * Set HTTP request & response handler 
 */
require __DIR__ . '/../bootstrap/Router.php';


//@TODO run background scripts after sending response to client