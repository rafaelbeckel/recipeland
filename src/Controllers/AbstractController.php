<?php

declare(strict_types=1);

namespace Recipeland\Controllers;

use BadMethodCallException;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Recipeland\Interfaces\ControllerInterface;
use Psr\Http\Message\ServerRequestInterface as RequestInterface;
use Psr\Http\Server\RequestHandlerInterface as HandlerInterface;

/**
 * A Controller in Recipeland is a special kind of middleware,
 * intended to be the last one called in the Middleware Stack.
 *
 * Instead of doing the traditional __call black magic hackery,
 * it implements the default handler interface from PSR-7 and
 * additionally provides a set of useful specific methods.
 */
abstract class AbstractController implements ControllerInterface, MiddlewareInterface
{
    protected $action;
    protected $response;
    protected $arguments = [];
    protected $middleware = [];
    protected $queryParams = [];

    final public function __construct(
        string $action,
        array $arguments = []
    ) {
        $this->action = $action;
        $this->arguments = $arguments;
        $this->response = new Response();
    }

    public function __call($method, $parameters)
    {
        throw new BadMethodCallException("Method [$method] does not exist.");
    }

    public function getMiddleware(): array
    {
        return $this->middleware;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function setQueryParams(array $params): void
    {
        $this->queryParams = $params;
    }

    public function getQueryParam(string $key, $default = '')
    {
        $param = null;

        return $this->queryParams[$key] ?? $default;
    }

    public function setStatus(int $code): void
    {
        $this->response = $this->response->withStatus($code);
    }

    public function setResponseBody(string $body): void
    {
        $stream = $this->response->getBody();
        $stream->write($body);

        $this->response = $this->response->withBody($stream);
    }

    public function setJsonResponse(array $json): void
    {
        $this->response = $this->response->withHeader(
                                               'Content-type',
                                               'application/json;charset=utf-8'
                                           );

        $this->setResponseBody(json_encode($json));
    }

    final public function process(RequestInterface $request, HandlerInterface $next): ResponseInterface
    {
        $this->setQueryParams($request->getQueryParams());

        // Call the action set by the router
        $actionName = $this->action;
        if (method_exists($this, $actionName)) {
            $this->$actionName($request, ...array_values($this->getArguments()));
        }

        // We won't call $next, so this will be the last response.
        // It will return back to upper middleware layers.
        return $this->response;
    }
}
