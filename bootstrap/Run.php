<?php
use Recipeland\Http\Request;
use Recipeland\Http\Router;

$request = Request::start();
$routes = include('../src/routes.php');

$r = new Router($routes);
$r->go($request);