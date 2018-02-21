<?php

error_reporting(E_ALL);
require __DIR__ . '/../vendor/autoload.php';

$env = new Dotenv\Dotenv(__DIR__.'/..');
$env->load();

$handler = new Whoops\Run;

if (getenv('ENVIRONMENT') == 'development') {
    $handler->pushHandler(new Whoops\Handler\PrettyPageHandler);
    
} else {
    $handler->pushHandler(function($error){
        // @todo log the error and send alert to developers
        echo "Some error message";
    });
    
}
$handler->register();

echo "Hi!";