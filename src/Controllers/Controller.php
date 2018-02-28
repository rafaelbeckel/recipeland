<?php

namespace Recipeland\Controllers;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Recipeland\Interfaces\ControllerInterface;
use Psr\Http\Message\ServerRequestInterface as RequestInterface;
use Psr\Http\Server\RequestHandlerInterface as HandlerInterface;
use Psr\Http\Server\MiddlewareInterface;


/**
 * A Controller in Recipeland is a special kind of middleware, 
 * intended to be the last one called in the Middleware Stack.
 * 
 * Instead of doing the traditional __call black magic hackery, 
 * it implements the default handler interface from PSR-7 and
 * additionally provides a set of useful specific methods.
 * 
 * In our current implementation, the controller is first instantiated
 * by the Router, who calls the setAction() method to store the name
 * of the method to be called. Then, the controller is injected in the
 * end of the Middleware Stack, and the process() method will call 
 * the actual action method.
 */
abstract class Controller implements MiddlewareInterface, ControllerInterface
{
    protected $request;
    protected $response;
    
    protected $action = 'defaultAction';
    protected $arguments = [];
    
    public function __construct(ResponseInterface $response = null) 
    {
        if ($response) {
            $this->response = $response;
        } else {
            $this->response = new Response;
        }
    }
    
    public function __call($method, $parameters)
    {
        throw new BadMethodCallException("Method [$method] does not exist.");
    }
    
    public function defaultAction(): void
    {
        // do nothing
    }
    
    public function setAction(string $action): void
    {
        $this->action = $action;
    }
    
    public function getAction(): string
    {
        return $this->action;
    }
    
    public function setArguments(array $arguments): void
    {
        $this->arguments = $arguments;
    }
    
    public function getArguments(): string
    {
        return $this->arguments;
    }
    
    public function setStatus(int $code): void
    {
        $this->response = $this->response->withStatus($code);
    }
    
    public function send(string $body): void
    {
        $stream = $this->response->getBody();
        $stream->write($body);
        
        $this->response = $this->response->withBody($stream);
    }
    
    public function sendJson(array $json): void
    {
        $this->send(json_encode($json));
    }
    
    public function process(RequestInterface $request, HandlerInterface $next): ResponseInterface
    {
        // Call the action set by the router
        $action = $this->action;
        if (method_exists($this, $action))
            $this->$action($this->arguments);
        
        // We won't call $next, so this will be the last response.
        // It will return back to upper middleware layers.
        return $this->response;
    }
}
