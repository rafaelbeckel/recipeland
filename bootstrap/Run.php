<?php
use Recipeland\Http\Request;
use Recipeland\Http\Router;

$request = Request::start();

$routes = include('../src/routes.php');
$router = new Router($routes);

$response = $router->go($request);
$response->send();