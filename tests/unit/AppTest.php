<?php

namespace Recipeland;

use GuzzleHttp\Psr7\ServerRequest as Request;
use Recipeland\Interfaces\RouterInterface;
use Psr\Http\Message\ResponseInterface;
use Recipeland\Controllers\Controller;
use PHPUnit\Framework\TestCase;
use Recipeland\Http\Router;
use Recipeland\App;
use \Mockery as m;

class AppTest extends TestCase
{
    public function test_go_method_returns_psr7_Response_instance()
    {
        $request = new Request( 'GET', '/foo' );
        
        $router = m::mock(RouterInterface::class);
        $router->shouldReceive('getControllerFor')
               ->with($request)->once()
               ->andReturn(m::spy(Controller::class));
        
        $app = new App($router);
        
        $response = $app->go($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
    }
}