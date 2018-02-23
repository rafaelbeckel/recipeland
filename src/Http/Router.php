<?php

namespace Recipeland\Http;

use Assert\Assertion;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use \InvalidArgumentException;
use Recipeland\Interfaces\FactoryInterface;
use Recipeland\Controllers\ControllerFactory;
use Symfony\Component\HttpFoundation\Request;

class Router
{
    //@TODO pull from lang file
<<<<<<< HEAD
    const EMPTY_ARRAY = "Routes Array cannot be empty";
    
    protected $request;
    protected $routes;
    protected $controllerFactory;
    
    private $_HTTP_Methods = [
=======
    const EMPTY_ARRAY = "Route collection array cannot be empty";
    const INVALID_ELEMENT_COUNT = "Route array must have 3 elements";
    const FIRST_ELEMENT_MUST_BE_REQUEST_METHOD = "First element must be Request Method ('GET', 'POST', etc.)";
    const SECOND_ELEMENT_MUST_BE_URL_PATH = "Second element must be URL Path";
    const THIRD_ELEMENT_MUST_BE_CONTROLLER_AND_ACTION = "Third element must be in the format Controller@action";
    const URL_PATH_PATTERN = "|(\/)([\w\/\[\]\{\}]*)(\??[\w\/\[\]\{\}]+\=[\w\/\[\]\{\}]+)*(\&?[\w\/\[\]\{\}]+\=[\w\/\[\]\{\}]+)*|i";
    const AT_PATTERN = "|[^@]*@[^@]*|";
    
    protected $routes;
    protected $request;
    protected $controllerFactory;
    
    private $HTTP_Methods = [
>>>>>>> 03_implement_the_router
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
    
    public function __construct(Array $routes)
    {
<<<<<<< HEAD
        $this->validateRoutesArray($routes);
        
        $this->request = $request;
        $this->routes = $routes;
        $this->setControllerFactory(new ControllerFactory);
=======
        $this->setControllerFactory(new ControllerFactory);
        $this->setRoutes($routes);
>>>>>>> 03_implement_the_router
    }
    
    
    public function go(Request $request): void
    {
        $this->request = $request;
        list($status, $route, $arguments) = $this->parseRequest();
        
        if ($status == Dispatcher::NOT_FOUND)
            $this->callController('Errors@not_found');
                
        if ($status == Dispatcher::METHOD_NOT_ALLOWED)
            $this->callController('Errors@method_not_allowed');
                
        if ($status == Dispatcher::FOUND)
            $this->callController($route, $arguments);
    }
    
    
<<<<<<< HEAD
    public function setControllerFactory(FactoryInterface $factory)
=======
    public function setControllerFactory(FactoryInterface $factory): void
>>>>>>> 03_implement_the_router
    {
        $this->controllerFactory = $factory;
    }
    
    
<<<<<<< HEAD
    protected function dispatch()
=======
    public function setRoutes(Array $routes): void
    {
        $this->validateRoutes($routes);
        $this->routes = $routes;
    }
    
    
    protected function parseRequest(): array
>>>>>>> 03_implement_the_router
    {
        $path = $this->request->getPathInfo();
        $httpMethod = $this->request->getMethod();
        
<<<<<<< HEAD
        $return = $dispatcher->dispatch($httpMethod, $path);
        
        $safe_return = array_replace([0,'',[]], $return);
        return $safe_return;
=======
        $dispatcher = $this->getDispatcher();
        $return = $dispatcher->dispatch($httpMethod, $path);
        
        return array_replace([0,'',[]], $return);
>>>>>>> 03_implement_the_router
    }
    
    
    protected function getDispatcher(): Dispatcher
    {
        return \FastRoute\simpleDispatcher(function(RouteCollector $routes){
            foreach ($this->routes as $route) {
                list($method, $path, $arguments) = $route;
                $routes->addRoute($method, $path, $arguments);
            }
        });
    }
    
    
    protected function callController(string $route, Array $arguments=[]): void
    {
        list($className, $method) = explode("@", $route);
        try {
            $controller = $this->controllerFactory->build($className);
            $controller->$method($arguments);
            
        } catch (Exception $e) {
            $this->callController('Errors@not_found');
        }
    }
    
    
<<<<<<< HEAD
    protected function validateRoutesArray(Array $routes) 
=======
    protected function validateRoutes(Array $routes): void
>>>>>>> 03_implement_the_router
    {
        if (empty($routes))
            throw new InvalidArgumentException(self::EMPTY_ARRAY);
        
        foreach ($routes as $route)
<<<<<<< HEAD
            if (!is_array($route) || empty($route) || count($route) !== 3)
                throw new InvalidArgumentException();
            
            // First item must be the HTTP Method
            //if ($)
                
=======
            $this->validateRoute($route);
    }
    
    
    private function validateRoute(Array $route): void
    {
        if (count($route) !== 3)
            throw new InvalidArgumentException(self::INVALID_ELEMENT_COUNT);
        
        if (! in_array($route[0], $this->HTTP_Methods))
            throw new InvalidArgumentException(self::FIRST_ELEMENT_MUST_BE_REQUEST_METHOD);
            
        if (! preg_match(self::URL_PATH_PATTERN, $route[1]))
            throw new InvalidArgumentException(self::SECOND_ELEMENT_MUST_BE_URL_PATH);
            
        if (! preg_match(self::AT_PATTERN, $route[2]))
            throw new InvalidArgumentException(self::THIRD_ELEMENT_MUST_BE_CONTROLLER_AND_ACTION);
>>>>>>> 03_implement_the_router
    }
}