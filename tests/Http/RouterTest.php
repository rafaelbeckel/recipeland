<?php
namespace Recipeland\Http;

use Recipeland\Controllers\ControllerFactory;
use Recipeland\Controllers\Controller;
use PHPUnit\Framework\TestCase;
use \InvalidArgumentException;
use Recipeland\Http\Request;
use Recipeland\Http\Router;
use \Mockery as m;
use \TypeError;

class RouterTest extends TestCase
{
    
    public function test_Router_calls_the_right_controller_and_action()
    {
        $request = Request::create( '/foo',  'GET' );
        
        $routes = [[ 'GET'   , '/foo'  , 'Bar@baz' ]];
        
        $factory = m::mock(ControllerFactory::class);
        $controller = m::spy(Controller::class);
        
        // Router will ask the factory to build the correct Controller 
        $factory->shouldReceive('build')
                ->with('Bar')->once()
                ->andReturn($controller); 
                    
        // Let's call our router
        $router = new Router($routes);
        $router->setControllerFactory($factory);
        $router->go($request);
        
        // Router needs to call the right action in the controller
        $controller->shouldHaveReceived('baz')->once();
        
        //Ugly workaround for Mockery BUG #785
        $this->addToAssertionCount(1);
    }
    
    
    public function test_Router_calls_non_existent_route()
    {
        $request = Request::create( '/fooo',  'GET' );
        
        $routes = [[ 'GET'   , '/foo'  , 'Bar@baz' ]];
        
        $factory = m::mock(ControllerFactory::class);
        $controller = m::spy(Controller::class);
        
        // Router will ask the factory to build the Error Controller 
        $factory->shouldReceive('build')
                ->with('Errors')->once()
                ->andReturn($controller); 
                    
        // Let's call our router
        $router = new Router($routes);
        $router->setControllerFactory($factory);
        $router->go($request);
        
        // Router will call not_found action
        $controller->shouldHaveReceived('not_found')->once();
        
        //Ugly workaround for Mockery BUG #785
        $this->addToAssertionCount(1);
    }
    
    
    public function test_Router_calls_invalid_http_verb()
    {
        $request = Request::create( '/foo',  'POST' );
        
        $routes = [[ 'GET'   , '/foo'  , 'Bar@baz' ]];
        
        $factory = m::mock(ControllerFactory::class);
        $controller = m::spy(Controller::class);
        
        // Router will ask the factory to build the Error Controller 
        $factory->shouldReceive('build')
                ->with('Errors')->once()
                ->andReturn($controller);
                    
        // Let's call our router
        $router = new Router($routes);
        $router->setControllerFactory($factory);
        $router->go($request);
        
        // Router will call method_not_allowed action
        $controller->shouldHaveReceived('method_not_allowed')->once();
        
        //Ugly workaround for Mockery BUG #785
        $this->addToAssertionCount(1);
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
            [ 'GET'   , '/foo'            , 'Recipes@get'         ],
            [ 'POST'  , '/foo'            , 'Recipes@create'      ],
            [ 'PUT'   , 'Recipes@update' ]  //Missing string
        ];
        
        $router = new Router($routes);
    }
    
    
    public function test_Route_array_validation_extra_string()
    {
        $this->expectException(InvalidArgumentException::class);
        
        $routes = [
            [ 'GET'   , '/foo'  , 'Recipes@get'    ],
            [ 'POST'  , '/foo'  , 'Recipes@create' ],
            [ 'PUT'   , '/foo'  , 'Recipes@update', 'Too Many Strings']
        ];
        
        $router = new Router($routes);
    }
    
    
    public function test_Route_array_validation_first_element()
    {
        $this->expectException(InvalidArgumentException::class);
        
        $routes = [
            [ 'I am not an HTTP Verb'   , '/foo'  , 'Recipes@get'    ],
        ];
        
        $router = new Router($routes);
    }
    
    
    public function test_Route_array_validation_second_element()
    {
        $this->expectException(InvalidArgumentException::class);
        
        $routes = [
            [ 'GET'   , '???'  , 'Recipes@get'    ],
        ];
        
        $router = new Router($routes);
    }
    
    
    public function test_Route_array_validation_third_element()
    {
        $this->expectException(InvalidArgumentException::class);
        
        $routes = [
            [ 'GET'   , '/foo'  , 'No_Symbol'    ],
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