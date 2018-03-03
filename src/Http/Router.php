<?php

namespace Recipeland\Http;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Psr\Http\Server\MiddlewareInterface;
use Recipeland\Interfaces\RouterInterface;
use Recipeland\Interfaces\FactoryInterface;
use Recipeland\Interfaces\ValidatorInterface;
use Recipeland\Controllers\ControllerFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Recipeland\Helpers\Validators\RoutesArrayValidator;
use Psr\Http\Message\ServerRequestInterface as RequestInterface;

class Router implements RouterInterface
{
    protected $routes;
    protected $request;
    protected $validator;
    protected $controller;
    protected $controllerFactory;
    
    
    public function __construct(array $routes, FactoryInterface $factory = null, ValidatorInterface $validator = null)
    {
        $this->setControllerFactory($factory);
        $this->setValidator($validator);
        $this->setRoutes($routes);
    }
    
    
    public function getControllerFor(RequestInterface $request): MiddlewareInterface
    {
        $this->request = $request;
        $route = $this->parseRequest();

        $this->setController($route['path'], $route['arguments']);
        return $this->controller;
    }
    
    
    public function setControllerFactory(FactoryInterface $factory = null): void
    {
        $this->controllerFactory = $factory ?: new ControllerFactory();
    }
    
    
    public function setValidator(ValidatorInterface $validator = null): void
    {
        $this->validator = $validator ?: new RoutesArrayValidator();
    }
    
    
    public function setRoutes(array $routes): void
    {
        $this->validator->validate($routes);
        $this->routes = $routes;
    }
    
    
    protected function parseRequest(): array
    {
        list($status, $path, $arguments) = $this->dispatch();
        
        if ($status == Dispatcher::NOT_FOUND) {
            $path = 'Errors@not_found';
        }
        
        if ($status == Dispatcher::METHOD_NOT_ALLOWED) {
            $path = 'Errors@method_not_allowed';
        }
        
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
        return \FastRoute\simpleDispatcher(function (RouteCollector $routes) {
            foreach ($this->routes as $route) {
                list($method, $path, $arguments) = $route;
                $routes->addRoute($method, $path, $arguments);
            }
        });
    }
    
    
    protected function setController(string $route, array $arguments=[]): void
    {
        list($className, $method) = explode("@", $route);
        try {
            $this->controller = $this->controllerFactory->build($className);
            $this->controller->setAction($method);
            $this->controller->setArguments($arguments);
        } catch (Exception $e) {
            if ($className == 'Errors') {
                throw new RuntimeException(self::ERROR_CONTROLLER_NOT_FOUND);
            }
                
            $this->setController('Errors@not_found');
        }
    }
}
