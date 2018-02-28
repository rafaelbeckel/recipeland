<?php

namespace Recipeland\Http;

use Assert\Assertion;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use \InvalidArgumentException;
use Psr\Http\Server\MiddlewareInterface;
use Recipeland\Interfaces\RouterInterface;
use Recipeland\Interfaces\FactoryInterface;
use Psr\Http\Message\ServerRequestInterface as RequestInterface;
use Recipeland\Controllers\ControllerFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class Router implements RouterInterface
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
    protected $controller;
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
    
    
    public function __construct(Array $routes, FactoryInterface $factory = null)
    {
        $this->setRoutes($routes);
        
        if (! $factory)
            $this->setControllerFactory(new ControllerFactory);
    }
    
    
    public function getControllerFor(RequestInterface $request): MiddlewareInterface
    {
        $this->request = $request;
        $route = $this->parseRequest();

        $this->setController($route['path'], $route['arguments']);
        return $this->controller;
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
        list($status, $path, $arguments) = $this->dispatch();
        
        if ($status == Dispatcher::NOT_FOUND)
            $path = 'Errors@not_found';
        
        if ($status == Dispatcher::METHOD_NOT_ALLOWED)
            $path = 'Errors@method_not_allowed';
        
        return ['path'=>$path, 'arguments'=>$arguments];
    }
    
    
    protected function dispatch(): array
    {
        $path = $this->request->getRequestTarget();
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
    
    
    protected function setController(string $route, Array $arguments=[]): void
    {
        list($className, $method) = explode("@", $route);
        try {
            $this->controller = $this->controllerFactory->build($className);
            $this->controller->setAction($method);
            $this->controller->setArguments($arguments);
            
        } catch (Exception $e) {
            $this->setController('Errors@not_found');
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