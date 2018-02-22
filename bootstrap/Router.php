<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;

define('CONTROLLERS_PATH', 'Recipeland\Controllers\\');

$request = Request::createFromGlobals();
$response = new Response();

$dispatcher = FastRoute\simpleDispatcher(function(RouteCollector $r){
    $routes = include('../src/Routes.php');
    foreach ($routes as $route) {
        $r->addRoute($route[0], $route[1], $route[2]);
    }
});

$routeInfo = $dispatcher->dispatch($request->getMethod(), $request->getPathInfo());

// @TODO - replace the route
switch ($routeInfo[0]) {
    case Dispatcher::NOT_FOUND:
        $response->setContent('Error 404 - Page Not Found');
        $response->setStatusCode(Response::HTTP_NOT_FOUND);
        break;
    case Dispatcher::METHOD_NOT_ALLOWED:
        $response->setContent('Error 405 - Method not Allowed');
        $response->setStatusCode(Response::HTTP_METHOD_NOT_ALLOWED);
        break;
    case Dispatcher::FOUND:
        $controller = explode("@", $routeInfo[1]);
        $className = CONTROLLERS_PATH.$controller[0];
        
        $method = $controller[1];
        $vars = $routeInfo[2];
        
        $class = new $className;
        $class->$method($vars);
        break;
}

$response->send();