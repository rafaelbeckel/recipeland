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
        $this->setControllerFactory(new ControllerFactory);
        $this->setRoutes($routes);
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
    
    
    public function setControllerFactory(FactoryInterface $factory): void
    {
        $this->controllerFactory = $factory;
    }
    
    
    public function setRoutes(Array $routes): void
    {
        $this->validateRoutes($routes);
        $this->routes = $routes;
    }
    
    
    protected function parseRequest(): array
    {
        $path = $this->request->getPathInfo();
        $httpMethod = $this->request->getMethod();
        
        $dispatcher = $this->getDispatcher();
        $return = $dispatcher->dispatch($httpMethod, $path);
        
        return array_replace([0,'',[]], $return);
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
    
    
    protected function validateRoutes(Array $routes): void
    {
        if (empty($routes))
            throw new InvalidArgumentException(self::EMPTY_ARRAY);
        
        foreach ($routes as $route)
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
    }
}