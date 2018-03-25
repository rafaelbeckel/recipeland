<?php

declare(strict_types=1);

namespace Recipeland\Helpers;

use GuzzleHttp\Psr7\Response;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Recipeland\Interfaces\StackInterface;
use Recipeland\Interfaces\FactoryInterface;
use Psr\Http\Message\ServerRequestInterface as RequestInterface;
use Psr\Http\Server\RequestHandlerInterface as HandlerInterface;

abstract class Stack implements StackInterface, HandlerInterface
{
    protected $items = [];
    protected $factory;

    public function __construct(FactoryInterface $factory, array $items = null)
    {
        $this->factory = $factory;

        if ($items) {
            $this->items = $items;
        }
    }

    public function getAll(): array
    {
        return $this->items;
    }

    public function append($item)
    {
        array_push($this->items, $item);
    }

    public function prepend($item)
    {
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

    public function movePointerToLastItem()
    {
        end($this->items);
    }

    public function getCurrentItem()
    {
        return current($this->items);
    }

    public function movePointerToNextItem()
    {
        next($this->items);
    }

    public function movePointerToPreviousItem()
    {
        prev($this->items);
    }

    public function handle(RequestInterface $request): ResponseInterface
    {
        $current = $this->getCurrentItem();
        $middleware = $this->getInstanceOf($current);
        $this->movePointerToNextItem();

        return $middleware->process($request, $this);
    }

    private function getInstanceOf($class): MiddlewareInterface
    {
        if (is_string($class) && class_exists($class)) {
            $middleware = $this->factory->build($class);
        }

        if (is_object($class) && $class instanceof MiddlewareInterface) {
            $middleware = $class;
        }

        if (empty($middleware) || !$middleware instanceof MiddlewareInterface) {
            throw new InvalidArgumentException('Middleware not found');
        }

        return $middleware;
    }
}
