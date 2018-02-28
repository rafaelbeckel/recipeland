<?php

namespace Recipeland;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Recipeland\Interfaces\StackInterface;
use Psr\Http\Message\ServerRequestInterface as RequestInterface;
use Psr\Http\Server\RequestHandlerInterface as HandlerInterface;

abstract class Stack implements StackInterface, HandlerInterface
{
    protected $items = [];
    
    public function __construct(array $items = null)
    {
        if ($items)
            $this->items = $items;
        
        foreach($this->items as $key => $item)
            $this->items[$key] = $this->getInstanceOf($item);
    }
    
    public function getAll(): array
    {
        return self::items;
    }
    
    public function append($item) {
        $object = $this->getInstanceOf($item);
        array_push($this->items, $item);
    }
    
    public function prepend($item) {
        $object = $this->getInstanceOf($item);
        array_unshift($this->items, $item);
    }
    
    public function shift()
    {
        array_shift($this->items);
    }
    
    public function pop()
    {
        array_pop($this->items);
    }
    
    public function resetPointerToFirstItem()
    {
        reset($this->items);
    }
    
    public function getCurrentItem()
    {
        return current($this->items);
    }
    
    public function movePointerToNextItem()
    {
        next($this->items);
    }
    
    public function handle(RequestInterface $request): ResponseInterface
    {
        $current = $this->getCurrentItem();
        
        if ($current === false) // Last item
            return new Response();
        
        $middleware = $this->getInstanceOf($current);
        
        $this->movePointerToNextItem();
        
        return $middleware->process($request, $this); 
    }
    
    private function getInstanceOf($class): MiddlewareInterface
    {
        if (is_string($class) && class_exists($class))
            $middleware = new $class();
            
        if (is_object($class) && $class instanceof MiddlewareInterface)
            $middleware = $class;
        
        if (empty($middleware) || ! $middleware instanceof MiddlewareInterface)
            $middleware = $this->createEmptyMiddleware();
        
        return $middleware;
    }
    
    private function createEmptyMiddleware(): MiddlewareInterface
    {
        return new class implements MiddlewareInterface {
            
            public function process(RequestInterface $request, HandlerInterface $next): ResponseInterface
            {
                return $next->handle($request);
            }
            
        };
    }
    
}