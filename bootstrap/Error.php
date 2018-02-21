<?php
use Whoops\Run as ErrorHandler;

error_reporting(E_ALL);

/**
 * Register the error handler for friendly error messages
 */
$err = new ErrorHandler;

if (getenv('ENVIRONMENT') == 'development') {
    $err->pushHandler(new Whoops\Handler\PrettyPageHandler);
    
} else {
    $err->pushHandler(function($e){
        /**
         * Some generic error message for production environment
         */
        echo "
        Swigert: \"Okay, Houston, we've had a problem here.\"       \n
        Lousma:  \"This is Houston. Say again, please.\"            \n
        Lovell:  \"Uh, Houston, we've had a problem.\"              \n
        Lovell:  \"We've had a MAIN B BUS UNDERVOLT.\"              \n
        Lousma:  \"Roger, MAIN B UNDERVOLT.\"                       \n
        Lousma:  \"Okay, stand by, Thirteen, we're looking at it.\" \n
        ";
        
        // @TODO log the error and send email to developers
        
    });
}


$err->register();