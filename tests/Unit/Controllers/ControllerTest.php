<?php

namespace Tests\Unit\Controllers;

use Psr\Http\Message\ServerRequestInterface as RequestInterface;
use Psr\Http\Server\RequestHandlerInterface as HandlerInterface;
use Recipeland\Controllers\AbstractController as Controller;
use Recipeland\Interfaces\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface as Logger;
use Tests\Unit\Http\Request\IWantThis;
use GuzzleHttp\Psr7\ServerRequest;
use Recipeland\Helpers\Factory;
use Tests\TestSuite;

class ControllerTest extends TestSuite
{
    protected $handler;
    protected $logger;
    
    public function setUp()
    {
        $this->request = new ServerRequest('GET', '/foo');
        $this->handler = $this->createMock(HandlerInterface::class);
    }
    
    protected function buildController($action, $arguments): Controller
    {
        $logger = $this->createMock(Logger::class);
        
        $class = new class($action, $arguments, $logger) extends Controller {
            public function set_status(Request $request)
            {
                $this->setStatus(418);
            }
            
            public function set_header(Request $request, $key, $value)
            {
                $this->setHeader($key, $value);
            }
            
            public function set_response_body(Request $request, $foo)
            {
                $this->setResponseBody($foo);
            }
            
            public function json_response(Request $request, ...$numbers)
            {
                $this->setJsonResponse($numbers);
            }
            
            public function specialized_request(IWantThis $request, $phpunit)
            {
                $this->setJsonResponse([$request->getParam('foo') => 'baz']);
            }
        };
        
        return $class;
    }

    public function test_set_status()
    {
        echo 'Controller - test setStatus';
        
        $controller = $this->buildController('set_status', []);
        $response = $controller->process($this->request, $this->handler);
        $this->assertEquals(418, $response->getStatusCode());
    }
    
    public function test_response_type()
    {
        echo 'Controller - test PSR-7 response';
        
        $controller = $this->buildController('set_status', []);
        $response = $controller->process($this->request, $this->handler);
        $this->assertInstanceOf(Response::class, $response);
    }
    
    public function test_set_header()
    {
        echo 'Controller - test setHeader';
        
        $controller = $this->buildController('set_header', ['foo', 'bar']);
        $response = $controller->process($this->request, $this->handler);
        $this->assertEquals(['foo'=>['bar']], $response->getHeaders());
    }

    public function test_set_response_body()
    {
        echo 'Controller - test setResponseBody';
        
        $controller = $this->buildController('set_response_body', ['My Body']);
        $response = $controller->process($this->request, $this->handler);
        $this->assertEquals('My Body', (string) $response->getBody());
    }

    public function test_set_json_response()
    {
        echo 'Controller - test setJsonResponse';
        
        $controller = $this->buildController('json_response', [1,2,3]);
        $response = $controller->process($this->request, $this->handler);
        $this->assertEquals(json_encode([1,2,3]), (string) $response->getBody());
    }
    
    public function test_upgrade_request()
    {
        echo 'Controller - test upgrade Request object';
        
        $request = $this->request->withQueryParams(['foo'=>'bar']);
        $controller = $this->buildController('specialized_request', [$this]);
        $response = $controller->process($request, $this->handler);
        
        $this->assertEquals(json_encode(['bar' => 'baz']), (string) $response->getBody());
    }
    
    public function test_failing_upgrade_request()
    {
        echo 'Controller - test failing upgrade Request object';
        
        $request = $this->request->withQueryParams(['bar'=>'baz']); //needs 'foo'
        $controller = $this->buildController('specialized_request', [$this]);
        
        $response = $controller->process($request, $this->handler);
        
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals(json_encode(['error' => 'Unauthorized']), (string) $response->getBody());
    }
}
