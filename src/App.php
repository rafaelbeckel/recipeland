<?php

declare(strict_types=1);

namespace Recipeland;

use Psr\Http\Message\ServerRequestInterface as RequestInterface;
use Recipeland\Interfaces\RouterInterface;
use Recipeland\Interfaces\SenderInterface;
use Recipeland\Interfaces\StackInterface;
use Psr\Http\Message\ResponseInterface;

final class App
{
    private $router;
    private $stack;
    private $sender;

    public function __construct(
        RouterInterface $router,
        StackInterface $stack,
        SenderInterface $sender
    ) {
        $this->router = $router;
        $this->stack = $stack;
        $this->sender = $sender;
    }

    public function go(RequestInterface $request): ResponseInterface
    {
        $controller = $this->router->getControllerFor($request);
        $this->stack->append($controller);
        $response = $this->processStack($request);

        return $response;
    }

    public function render(ResponseInterface $response)
    {
        $this->sender->send($response);
    }

    private function processStack(RequestInterface $request): ResponseInterface
    {
        $this->stack->resetPointerToFirstItem();

        return $this->stack->handle($request);
    }

    public function close($request, $response)
    {
        // You can implement calls to long-running processes here
        // Like sending e-mails,

        // $this->logger->info('Look, Ma! I am running in Background!');
        // $this->logger->info('Execution time: ');
    }
}
