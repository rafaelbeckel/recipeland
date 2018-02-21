<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
$request = Request::createFromGlobals();

$response = new Response();
$response->setContent('Hello Again!');
$response->send();


//@TODO run background scripts after sending response to client