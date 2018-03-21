<?php

namespace Tests\Unit;

use Recipeland\Controllers\AbstractController as Controller;
use GuzzleHttp\Psr7\ServerRequest as Request;
use Recipeland\Interfaces\RouterInterface;
use Recipeland\Interfaces\SenderInterface;
use Psr\Http\Message\ResponseInterface;
use Recipeland\Config;
use Recipeland\Stack;
use Tests\TestSuite;
use Recipeland\App;

class AppTest extends TestSuite
{
    protected $controller;
    protected $request;
    protected $router;
    protected $stack;
    protected $sender;

    public function setUp()
    {
        parent::setUp();

        $this->request = new Request('GET', '/foo');

        $this->controller = $this->createMock(Controller::class);
        $this->router = $this->createMock(RouterInterface::class);
        $this->stack = $this->createMock(Stack::class);
        $this->sender = $this->createMock(SenderInterface::class);

        $this->router->expects($this->once())
                     ->method('getControllerFor')
                     ->with($this->request)
                     ->willReturn($this->controller);

        $this->stack->expects($this->once())
                    ->method('append')
                    ->with($this->controller);

        $this->stack->expects($this->once())
                    ->method('resetPointerToFirstItem');

        $this->stack->expects($this->once())
                    ->method('handle')
                    ->with($this->request)
                    ->willReturn($this->createMock(ResponseInterface::class));
    }

    public function test_go_method_returns_psr7_Response_instance()
    {
        echo 'App: go() method must return a PSR-7 Response object';

        $app = new App($this->router, $this->stack, $this->sender);

        $response = $app->go($this->request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    public function test_render_method_delegates_Response_to_Sender()
    {
        echo 'App: render() method must delegate Response to Sender';

        $sender = $this->createMock(SenderInterface::class);

        $app = new App($this->router, $this->stack, $sender);
        $response = $app->go($this->request);

        $sender->expects($this->once())
               ->method('send')
               ->with($response);

        $app->render($response);
    }
}
