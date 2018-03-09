<?php

use Monolog\Logger;
use Whoops\Run as ErrorHandler;
use Monolog\Handler\StreamHandler;

error_reporting(E_ALL);


/**
 * Register the error handler for friendly error messages
 */
$err = new ErrorHandler;

$errorLog = new Logger('error');
$errorLog->pushHandler(new StreamHandler(__DIR__.'/../log/error.log', Logger::WARNING));

$accessLog = new Logger('access');
$accessLog->pushHandler(new StreamHandler(__DIR__.'/../log/access.log', Logger::INFO));


if (getenv('ENVIRONMENT') != 'production') {
    $err->pushHandler(new Whoops\Handler\PrettyPageHandler);
    
} else {
    $err->pushHandler(function($e){
        /**
         * Generic error message for production environment
         */
        echo "
            Swigert: \"Okay, Houston, we've had a problem here.\"       \n
            Lousma:  \"This is Houston. Say again, please.\"            \n
            Lovell:  \"Uh, Houston, we've had a problem.\"              \n
            Lovell:  \"We've had a MAIN B BUS UNDERVOLT.\"              \n
            Lousma:  \"Roger, MAIN B UNDERVOLT.\"                       \n
            Lousma:  \"Okay, stand by, Thirteen, we're looking at it.\" \n
        "; // @TODO pull this message from config or lang class
        
        $errorLog->error($e->getMessage());
        $errorLog->debug($e->getTraceAsString());
    });
}

$err->register();