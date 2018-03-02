<?php

namespace Tests\Http;

use GuzzleHttp\Psr7\ServerRequest as Request;
use Recipeland\Controllers\ControllerFactory;
use Recipeland\Controllers\Controller;
use PHPUnit\Framework\TestCase;
use \InvalidArgumentException;
use Recipeland\Http\Router;
use Tests\TestSuite;
use \Mockery as m;
use \TypeError;

class RouterTest extends TestSuite
{
    
    public function test_Router_controller_and_action()
    {
        echo "Router: should return the right controller and action from a routes Array and a Request";
        
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
    
    
    public function test_Router_controller_and_action_with_arguments()
    {
        echo "Router: should return the right controller and action, with arguments";
        
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
    
    
    public function test_Router_non_existent_route()
    {
        echo "Router: should call Error controller @ not_found action for a non-existent route";
        
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
        echo "Router: should call Error controller @ not_found action for a route with incomplete name";
        
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
        echo "Router: should call Error controller @ method_not_allowed action for a wrong HTTP method call";
        
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
}