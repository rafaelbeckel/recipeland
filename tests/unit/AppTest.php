<?php

namespace Recipeland;

use GuzzleHttp\Psr7\ServerRequest as Request;
use Recipeland\Interfaces\RouterInterface;
use Recipeland\Interfaces\StackInterface;
use Psr\Http\Message\ResponseInterface;
use Recipeland\Controllers\Controller;
use Recipeland\Http\Router;
use Tests\TestSuite;
use Recipeland\App;
use \Mockery as m;

class AppTest extends TestSuite
{
    public function test_go_method_returns_psr7_Response_instance()
    {
        echo "App: go() method must return a PSR-7 Response object";
        
        $request = new Request( 'GET', '/foo' );
        
        $controller = m::mock(Controller::class);
        
        $router = m::mock(RouterInterface::class);
        $router->shouldReceive('getControllerFor')
               ->with($request)->once()
               ->andReturn($controller);
               
        $stack = m::mock(StackInterface::class);
        $stack->shouldReceive('append')
              ->with($controller)->once()
              ->andReturn(m::spy(Controller::class))
              ->shouldReceive('resetPointerToFirstItem')
              ->shouldReceive('handle')
              ->with($request)->once()
              ->andReturn(m::mock(ResponseInterface::class));
        
        $app = new App($router, $stack);
        
        $response = $app->go($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }
}