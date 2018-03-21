<?php

namespace Tests\Unit\Controllers;

use Psr\Http\Message\ServerRequestInterface as RequestInterface;
use Psr\Http\Server\RequestHandlerInterface as HandlerInterface;
use Recipeland\Controllers\AbstractController as Controller;
use Recipeland\Interfaces\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Recipeland\Controllers\ControllerFactory;
use Psr\Log\LoggerInterface as Logger;
use Tests\Unit\Http\Request\IWantThis;
use DI\FactoryInterface as Container;
use Recipeland\Controllers\Errors;
use GuzzleHttp\Psr7\ServerRequest;
use Recipeland\Helpers\Factory;
use BadMethodCallException;
use ReflectionException;
use RuntimeException;
use Tests\TestSuite;

class ControllerTest extends TestSuite
{
    protected $handler;
    protected $logger;
    
    public function setUp()
    {
        parent::setUp();
        $this->request = new ServerRequest('GET', '/foo');
        $this->handler = $this->createMock(HandlerInterface::class);
    }
    
    protected function buildController($action, $arguments, $logger=null): Controller
    {
        $logger = $logger ?: $this->createMock(Logger::class);
        
        $class = new class($action, $arguments, $logger) extends Controller {
            public function set_status(Request $request)
            {
                $this->setStatus(418);
            }
            
            public function set_error(Request $request)
            {
                return $this->error('not_found');
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
            
            public function badly_defined_action(string $request)
            {
                // Will not be called
            }
        };
        
        return $class;
    }
    
    public function test_response_type()
    {
        echo 'Controller - test PSR-7 response';
        
        $controller = $this->buildController('set_status', []);
        $response = $controller->process($this->request, $this->handler);
        $this->assertInstanceOf(Response::class, $response);
    }
    
    public function test_set_status()
    {
        echo 'Controller - test setStatus';
        
        $controller = $this->buildController('set_status', []);
        $response = $controller->process($this->request, $this->handler);
        $this->assertEquals(418, $response->getStatusCode());
    }
    
    public function test_set_header()
    {
        echo 'Controller - test setHeader';
        
        $controller = $this->buildController('set_header', ['foo', 'bar']);
        $response = $controller->process($this->request, $this->handler);
        $this->assertEquals(['foo'=>['bar']], $response->getHeaders());
    }
    
    public function test_set_error()
    {
        echo 'Controller - test error method';
        
        $controller = $this->buildController('set_error', []);
        $response = $controller->process($this->request, $this->handler);
        $this->assertEquals(404, $response->getStatusCode());
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
        
        $request = new ServerRequest('POST', '/foo', [], json_encode(['foo'=>'bar']));
        $controller = $this->buildController('specialized_request', [$this]);
        $response = $controller->process($request, $this->handler);
        
        $this->assertEquals(json_encode(['bar' => 'baz']), (string) $response->getBody());
    }
    
    public function test_upgrade_request_non_existing_method()
    {
        echo 'Controller - test upgrade Request object with non-existing method';
        
        $this->expectException(ReflectionException::class);
        
        $request = new ServerRequest('POST', '/foo', [], json_encode(['foo'=>'bar']));
        $controller = $this->buildController('non_existent_action', [$this]);
        $response = $controller->process($request, $this->handler);
    }
    
    public function test_upgrade_direct_calling_non_existing_method()
    {
        echo 'Controller - test direct calling non-existing method';
        
        $this->expectException(BadMethodCallException::class);
        
        $request = new ServerRequest('POST', '/foo', [], json_encode(['foo'=>'bar']));
        $controller = $this->buildController('non_existent_action', [$this]);
        $response = $controller->non_existent_method($request);
    }
    
    public function test_badly_defined_method_exception()
    {
        echo 'Controller - test badly defined method exception';
        
        $logger = $this->createMock(Logger::class);
        $logger->expects($this->once())->method('error');
        
        $request = new ServerRequest('POST', '/foo', [], json_encode(['foo'=>'bar']));
        $controller = $this->buildController('badly_defined_action', [$this], $logger);
        $response = $controller->process($request, $this->handler);
        
        $this->assertEquals(404, $response->getStatusCode());
    }
    
    public function test_failing_upgrade_request()
    {
        echo 'Controller - test failing upgrade Request object';
        
        $request = new ServerRequest('POST', '/foo', [], json_encode(['bar'=>'baz'])); //needs 'foo'
        $controller = $this->buildController('specialized_request', [$this]);
        
        $response = $controller->process($request, $this->handler);
        
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertArraySubset(['error' => 'Unauthorized'], json_decode((string) $response->getBody(), true));
    }
    
    public function test_controller_factory()
    {
        echo 'Controller - test controller factory';
        
        $errors = $this->createMock(Errors::class);
        $container = $this->createMock(Container::class);
        
        $container->expects($this->at(0))
                  ->method('make')
                  ->with('log')
                  ->willReturn('foo');
                  
        $container->expects($this->at(1))
                  ->method('make')
                  ->with(Errors::class, [
                      'action' => 'my_error',
                      'arguments' => ['bar'],
                      'logger' => 'foo'
                  ])
                  ->willReturn($errors);
        
        $factory = new ControllerFactory($container);
        $factory->build('Errors', 'my_error', ['bar']);
    }
    
    public function test_controller_factory_non_existent()
    {
        echo 'Controller - test controller factory non existent class';
        
        $this->expectException(RuntimeException::class);
        
        $container = $this->createMock(Container::class);
        $factory = new ControllerFactory($container);
        $factory->build('NonExistentController');
    }
}
