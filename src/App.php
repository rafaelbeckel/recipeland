<?php

namespace Recipeland;

use Psr\Http\Message\ServerRequestInterface as RequestInterface;
use Psr\Http\Server\RequestHandlerInterface as HandlerInterface;
use Recipeland\Middleware\MiddlewareStack;
use Recipeland\Interfaces\RouterInterface;
use Recipeland\Interfaces\StackInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;

class App
{
    protected $router;
    protected $stack;
    
    public function __construct(RouterInterface $router, StackInterface $stack = null)
    {
        $this->setRouter($router);
        
        if ($stack) {
            $this->setStack($stack);
        } else {
            $this->setStack(new MiddlewareStack);
        }
    }
    
    public function go(RequestInterface $request): ResponseInterface
    {
        $controller = $this->router->getControllerFor($request);
        $this->stack->append($controller);
        $response = $this->processStack($request);
        
        return $response;
    }
    
    public function setRouter(RouterInterface $router): void
    {
        $this->router = $router;
    }
    
    public function setStack(StackInterface $stack): void
    {
        $this->stack = $stack;
    }
    
    public function processStack(RequestInterface $request): ResponseInterface
    {
        $this->stack->resetPointerToFirstItem();
        return $this->stack->handle($request);
    }
    
    public function close()
    {
        // do nothing... for now.
    }
}
