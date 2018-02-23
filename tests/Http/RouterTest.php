<?php
namespace Recipeland\Http;

use Recipeland\Interfaces\FactoryInterface;
use Recipeland\Controllers\Controller;
use PHPUnit\Framework\TestCase;
use \InvalidArgumentException;
use Recipeland\Http\Request;
use Recipeland\Http\Router;
use \Mockery as m;

class RouterTest extends TestCase
{
    public $request;
    
    public function setUp()
    {
        $this->request = Request::create( '/foo',  'GET' );
    }
    
    
    public function test_Router_calls_the_right_controller_and_action()
    {
        $routes = [
           // Method    URL Path  Controller@action
            [ 'GET'   , '/foo'  , 'Bar@baz' ]
        ];
        
        $factory = m::mock(FactoryInterface::class);
        $controller = m::spy(Controller::class);
        
        // Router will ask the factory to build the correct Controller 
        $factory->shouldReceive('build')->with('Bar')->once()
                    ->andReturn($controller); 
                    
        // Let's test our router
        $router = new Router($this->request, $routes);
        $router->setControllerFactory($factory);
        $router->go();
        
        // Router needs to call the right action in the controller
        $controller->shouldHaveReceived('baz')->once();
        
        //Ugly workaround for Mockery BUG #785
        $this->addToAssertionCount(1);
    }
    
    
    public function test_Route_should_not_accept_empty_routes_array()
    {
        $this->expectException(InvalidArgumentException::class);
        
        $routes = [];
        
        $router = new Router($this->request, $routes);
    }
    
    
    public function test_Route_array_validation_missing_string()
    {
        $this->expectException(InvalidArgumentException::class);
        
        $routes = [
            [ 'GET'   , '/foo'            , 'Recipes@get'         ],
            [ 'POST'  , '/foo'            , 'Recipes@create'      ],
            [ 'PUT'   , 'Recipes@update' ]   //Missing one string
        ];
        
        $router = new Router($this->request, $routes);
    }
    
    
    public function test_Route_array_validation_extra_string()
    {
        $this->expectException(InvalidArgumentException::class);
        
        $routes = [
            [ 'GET'   , '/foo'            , 'Recipes@get'         ],
            [ 'POST'  , '/foo'            , 'Recipes@create'      ],
            [ 'PUT'   , 'Recipes@update'  , 'Recipes@update', 'Too Many Strings']
        ];
        
        $router = new Router($this->request, $routes);
    }
    
    
    public function tearDown() {
        m::close();
    }
}


class StubController extends Controller
{
    public function get() {}
}