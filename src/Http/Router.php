<?php

namespace Recipeland\Http;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Recipeland\Controllers\Controller;
use Symfony\Component\HttpFoundation\Request;

class Router
{
    protected $request;
    protected $routes;
    
    
    public function __construct(Request $request, Array $routes) 
    {
        $this->request = $request;
        $this->routes = $routes; //@TODO validate Routes array
    }
    
    
    public function go()
    {
        list($status, $route, $arguments) = $this->dispatch();
        
        if ($status == Dispatcher::NOT_FOUND)
            $this->callController('Error@not_found');
                
        if ($status == Dispatcher::METHOD_NOT_ALLOWED)
            $this->callController('Error@method_not_allowed');
                
        if ($status == Dispatcher::FOUND)
            $this->callController($route, $arguments);
    }
    
    
    protected function dispatch()
    {
        $dispatcher = $this->getDispatcher();
        $path = $this->request->getPathInfo();
        $httpMethod = $this->request->getMethod();
        return $dispatcher->dispatch($httpMethod, $path);
    }
    
    
    protected function getDispatcher()
    {
        return \FastRoute\simpleDispatcher(function(RouteCollector $routes){
            foreach ($this->routes as $route) {
                list($method, $path, $arguments) = $route;
                $routes->addRoute($method, $path, $arguments);
            }
        });
    }
    
    
    protected function callController(string $route, Array $arguments=[])
    {
        list($className, $method) = explode("@", $route);
        $controller = Controller::getNamespace().'\\'.$className;
        $class = new $controller;
        $class->$method($arguments);
    }
}