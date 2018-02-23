<?php

namespace Recipeland\Http;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use \InvalidArgumentException;
use Recipeland\Interfaces\FactoryInterface;
use Recipeland\Controllers\ControllerFactory;
use Symfony\Component\HttpFoundation\Request;

class Router
{
    //@TODO pull from lang file
    const EMPTY_ARRAY = "Routes Array cannot be empty";
    
    protected $request;
    protected $routes;
    protected $controllerFactory;
    
    private $_HTTP_Methods = [
        'HEAD',
        'GET',
        'POST',
        'PUT',
        'PATCH',
        'DELETE',
        'PURGE',
        'OPTIONS',
        'TRACE',
        'CONNECT',
    ];
    
    public function __construct(Request $request, Array $routes) 
    {
        $this->validateRoutesArray($routes);
        
        $this->request = $request;
        $this->routes = $routes;
        $this->setControllerFactory(new ControllerFactory);
    }
    
    
    public function go()
    {
        list($status, $route, $arguments) = $this->dispatch();
        
        if ($status == Dispatcher::NOT_FOUND)
            $this->callController('Errors@not_found');
                
        if ($status == Dispatcher::METHOD_NOT_ALLOWED)
            $this->callController('Errors@method_not_allowed');
                
        if ($status == Dispatcher::FOUND)
            $this->callController($route, $arguments);
    }
    
    
    public function setControllerFactory(FactoryInterface $factory)
    {
        $this->controllerFactory = $factory;
    }
    
    
    protected function dispatch()
    {
        $dispatcher = $this->getDispatcher();
        $path = $this->request->getPathInfo();
        $httpMethod = $this->request->getMethod();
        
        $return = $dispatcher->dispatch($httpMethod, $path);
        
        $safe_return = array_replace([0,'',[]], $return);
        return $safe_return;
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
        try {
            $controller = $this->controllerFactory->build($className);
            $controller->$method($arguments);
            
        } catch (Exception $e) {
            $this->callController('Errors@not_found');
        }
    }
    
    
    protected function validateRoutesArray(Array $routes) 
    {
        if (empty($routes))
            throw new InvalidArgumentException(self::EMPTY_ARRAY);
        
        foreach ($routes as $route)
            if (!is_array($route) || empty($route) || count($route) !== 3)
                throw new InvalidArgumentException();
            
            // First item must be the HTTP Method
            //if ($)
                
    }
}