<?php
namespace Recipeland\Http;

use GuzzleHttp\Psr7\ServerRequest as Request;
use Recipeland\Controllers\ControllerFactory;
use Recipeland\Controllers\Controller;
use PHPUnit\Framework\TestCase;
use \InvalidArgumentException;
use Recipeland\Http\Router;
use \Mockery as m;
use \TypeError;

class RouterTest extends TestCase
{
    
    public function test_Router_calls_the_right_controller_and_action()
    {
        $request = new Request( 'GET', '/foo' );
        $router = new Router([[ 'GET', '/foo', 'Bar@baz' ]]);
        
        // Router will ask the factory to build the correct Controller 
        $factory = m::mock(ControllerFactory::class);
        $factory->shouldReceive('build')
                ->with('Bar')->once()
                ->andReturn(m::spy(Controller::class));
                
        // Let's call our router
        $router->setControllerFactory($factory);
        $controller = $router->getControllerFor($request);
        
        // Router should have called the right action in the controller
        $controller->shouldHaveReceived('setAction')->with('baz')->once();
        $controller->shouldHaveReceived('setArguments')->with([])->once();
        
        // Ugly workaround for Mockery BUG #785
        $this->addToAssertionCount(3); 
    }
    
    
    public function test_Router_calls_the_right_controller_and_action_with_arguments()
    {
        $request = new Request( 'GET', '/foo/1234/make/Coffee' );
        $router = new Router([[ 'GET', '/foo/{id}/make/{name}', 'Bar@baz' ]]);
        
        // Router will ask the factory to build the correct Controller 
        $factory = m::mock(ControllerFactory::class);
        $spyController = m::spy(Controller::class);
        $factory->shouldReceive('build')
                ->with('Bar')->once()
                ->andReturn($spyController);
                    
        // Let's call our router
        $router->setControllerFactory($factory);
        $controller = $router->getControllerFor($request);
        
        // Router should have called the right action in the controller
        $controller->shouldHaveReceived('setAction')->with('baz')->once();
        $controller->shouldHaveReceived('setArguments')->with(['id'=>'1234', 'name'=>'Coffee'])->once();
        
        // Ugly workaround for Mockery BUG #785
        $this->addToAssertionCount(3);
    }
    
    
    public function test_Router_calls_non_existent_route()
    {
        $request = new Request( 'GET', '/foooooooo' );
        $router = new Router([[ 'GET', '/foo', 'Bar@baz' ]]);
        
        // Router will ask the factory to build the Error Controller 
        $factory = m::mock(ControllerFactory::class);
        $factory->shouldReceive('build')
                ->with('Errors')->once()
                ->andReturn(m::spy(Controller::class)); 
                    
        // Let's call our router
        $router->setControllerFactory($factory);
        $controller = $router->getControllerFor($request);
        
        // Router will call not_found action
        $controller->shouldHaveReceived('setAction')->with('not_found')->once();
        $controller->shouldHaveReceived('setArguments')->with([])->once();
        
        // Ugly workaround for Mockery BUG #785
        $this->addToAssertionCount(3);
    }
    
    
    public function test_Router_calls_partial_route_name()
    {
        $request = new Request( 'GET', '/fo' );
        $router = new Router([[ 'GET', '/foo', 'Bar@baz' ]]);
        
        // Router will ask the factory to build the Error Controller 
        $factory = m::mock(ControllerFactory::class);
        $factory->shouldReceive('build')
                ->with('Errors')->once()
                ->andReturn(m::spy(Controller::class)); 
                    
        // Let's call our router
        $router->setControllerFactory($factory);
        $controller = $router->getControllerFor($request);
        
        // Router will call not_found action
        $controller->shouldHaveReceived('setAction')->with('not_found')->once();
        $controller->shouldHaveReceived('setArguments')->with([])->once();
        
        // Ugly workaround for Mockery BUG #785
        $this->addToAssertionCount(3);
    }
    
    
    public function test_Router_calls_invalid_http_verb()
    {
        $request = new Request( 'POST', '/foo' );
        $router = new Router([[ 'GET',  '/foo', 'Bar@baz' ]]);
        
        // Router will ask the factory to build the Error Controller 
        $factory = m::mock(ControllerFactory::class);
        $factory->shouldReceive('build')
                ->with('Errors')->once()
                ->andReturn(m::spy(Controller::class));
                    
        // Let's call our router
        $router->setControllerFactory($factory);
        $controller = $router->getControllerFor($request);
        
        // Router will call method_not_allowed action
        $controller->shouldHaveReceived('setAction')->with('method_not_allowed')->once();
        $controller->shouldHaveReceived('setArguments')->with([])->once();
        
        // Ugly workaround for Mockery BUG #785
        $this->addToAssertionCount(3);
    }
    
    
    public function test_Route_should_not_accept_non_array_arguments()
    {
        $this->expectException(TypeError::class);
        
        $routes = "I am not an array!";
        
        $router = new Router($routes);
    }
    
    
    public function test_Route_should_not_accept_empty_array()
    {
        $this->expectException(InvalidArgumentException::class);
        
        $routes = [];
        
        $router = new Router($routes);
    }
    
    
    public function test_Route_array_validation_missing_string()
    {
        $this->expectException(InvalidArgumentException::class);
        
        $routes = [
            [ 'GET' , '/foo'            , 'Recipes@get'         ],
            [ 'POST', '/foo'            , 'Recipes@create'      ],
            [ 'PUT' , 'Recipes@update' ]  //Missing string
        ];
        
        $router = new Router($routes);
    }
    
    
    public function test_Route_array_validation_extra_string()
    {
        $this->expectException(InvalidArgumentException::class);
        
        $routes = [
            [ 'GET' , '/foo', 'Recipes@get'    ],
            [ 'POST', '/foo', 'Recipes@create' ],
            [ 'PUT' , '/foo', 'Recipes@update', 'Too Many Strings']
        ];
        
        $router = new Router($routes);
    }
    
    
    public function test_Route_array_validation_first_element()
    {
        $this->expectException(InvalidArgumentException::class);
        
        $routes = [
            [ 'I am not an HTTP Verb', '/foo', 'Recipes@get'],
        ];
        
        $router = new Router($routes);
    }
    
    
    public function test_Route_array_validation_second_element()
    {
        $this->expectException(InvalidArgumentException::class);
        
        $routes = [
            [ 'GET', '???', 'Recipes@get'],
        ];
        
        $router = new Router($routes);
    }
    
    
    public function test_Route_array_validation_third_element()
    {
        $this->expectException(InvalidArgumentException::class);
        
        $routes = [
            [ 'GET', '/foo', 'No_Symbol'],
        ];
        
        $router = new Router($routes);
    }
    
    public function tearDown() {
        m::close();
    }
}


class StubController extends Controller
{
    public function get() {}
}