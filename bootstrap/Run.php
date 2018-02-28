<?php
use Recipeland\App;
use Recipeland\Http\Router;
use Recipeland\Http\Response\Sender;
use GuzzleHttp\Psr7\ServerRequest as Request;

// Setup our URL routes
$router = new Router(include('../src/routes.php'));
$app = new App($router);

// Do the magic
$request = Request::fromGlobals();
$response = $app->go($request);

// Send response to client
$sender = new Sender($response);
$sender->send($response);

// Run background jobs
$app->close($request, $response);