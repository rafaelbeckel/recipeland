<?php

declare(strict_types=1);

namespace Recipeland\Http;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use InvalidArgumentException;
use Psr\Http\Server\MiddlewareInterface;
use Recipeland\Interfaces\RouterInterface;
use Recipeland\Interfaces\FactoryInterface;
use Recipeland\Interfaces\ValidatorInterface;
use Psr\Http\Message\ServerRequestInterface as RequestInterface;

class Router implements RouterInterface
{
    protected $routes;
    protected $cacheFile;
    protected $validator;
    protected $controller;
    protected $controllerFactory;

    public function __construct(
        array $routes,
        FactoryInterface $factory,
        ValidatorInterface $validator,
        string $cacheFile = null
    ) {
        $this->controllerFactory = $factory;
        $this->validator = $validator;
        $this->cacheFile = $cacheFile;
        $this->setRoutes($routes);
    }

    public function getControllerFor(RequestInterface $request): MiddlewareInterface
    {
        $route = $this->parse($request);
        $this->setController($route['path'], $route['arguments']);

        return $this->controller;
    }

    public function setRoutes(array $routes): void
    {
        if ($this->validator->validate($routes)) {
            $this->routes = $routes;
        } else {
            throw new InvalidArgumentException($this->validator->getMessage());
        }
    }

    protected function parse(RequestInterface $request): array
    {
        [$status, $path, $arguments] = $this->dispatch($request);

        if ($status == Dispatcher::NOT_FOUND) {
            $path = 'Errors.not_found';
        }

        if ($status == Dispatcher::METHOD_NOT_ALLOWED) {
            $path = 'Errors.method_not_allowed';
        }

        return ['path' => $path, 'arguments' => $arguments];
    }

    protected function dispatch(RequestInterface $request): array
    {
        $httpMethod = $request->getMethod();
        $path = rtrim($request->getUri()->getPath(), '/');

        $dispatcher = $this->getDispatcher();
        $return = $dispatcher->dispatch($httpMethod, $path);

        return array_replace([0, '', []], $return);
    }

    protected function getDispatcher(): Dispatcher
    {
        if ($this->cacheFile) {
            return \FastRoute\cachedDispatcher($this->getRoutesClosure(), [
                'cacheFile' => $this->cacheFile,
            ]);
        } else {
            return \FastRoute\simpleDispatcher($this->getRoutesClosure());
        }
    }

    protected function getRoutesClosure()
    {
        return function (RouteCollector $collector) {
            foreach ($this->routes as $route) {
                [$method, $path, $arguments] = $route;
                $path = rtrim($path, '/');
                $collector->addRoute($method, $path, $arguments);
            }
        };
    }

    protected function setController(string $route, array $arguments = []): void
    {
        [$className, $method] = explode('.', $route);
        try {
            $this->controller = $this->controllerFactory->build(
                $className,
                $method,
                $arguments
            );
        } catch (Exception $e) {
            if ($className == 'Errors') {
                throw new RuntimeException(self::ERROR_CONTROLLER_NOT_FOUND);
            }
            $this->setController('Errors.not_found');
        }
    }
}
