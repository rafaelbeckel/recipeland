<?php

namespace Tests\Unit\Http;

use Recipeland\Controllers\AbstractController as Controller;
use Recipeland\Interfaces\ControllerInterface;
use GuzzleHttp\Psr7\ServerRequest as Request;
use Recipeland\Controllers\ControllerFactory;
use Psr\Http\Server\MiddlewareInterface;
use Recipeland\Http\Router;
use Tests\TestSuite;
use Mockery as m;

class RouterTest extends TestSuite
{
    public function test_Router_controller_and_action()
    {
        echo 'Router: should return the right controller and action from a routes Array and a Request';

        $request = new Request('GET', '/foo');
        $router = new Router([['GET', '/foo', 'Bar@baz']]);

        // Router will ask the factory to build the correct Controller
        $factory = m::mock(ControllerFactory::class);
        $factory->shouldReceive('build')
                ->with('Bar', 'baz', [])->once()
                ->andReturn(
                    m::mock(
                        Controller::class,
                        ['baz', []] // constructor arguments
                    )
                );

        // Let's call our router
        $router->setControllerFactory($factory);
        $controller = $router->getControllerFor($request);

        $this->assertInstanceOf(ControllerInterface::class, $controller);
        $this->assertInstanceOf(MiddlewareInterface::class, $controller);
    }

    public function test_Router_controller_and_action_with_arguments()
    {
        echo 'Router: should return the right controller and action, with arguments';

        $request = new Request('GET', '/foo/1234/make/Coffee');
        $router = new Router([['GET', '/foo/{id}/make/{name}', 'Bar@baz']]);

        // Router will ask the factory to build the correct Controller
        $factory = m::mock(ControllerFactory::class);
        $factory->shouldReceive('build')
                ->with('Bar', 'baz', ['id' => '1234', 'name' => 'Coffee'])->once()
                ->andReturn(m::mock(
                    Controller::class,
                    ['baz', ['id' => '1234', 'name' => 'Coffee']]
                ));

        // Let's call our router
        $router->setControllerFactory($factory);
        $controller = $router->getControllerFor($request);

        $this->assertInstanceOf(ControllerInterface::class, $controller);
        $this->assertInstanceOf(MiddlewareInterface::class, $controller);
    }

    public function test_Router_non_existent_route()
    {
        echo 'Router: should call Error controller @ not_found action for a non-existent route';

        $request = new Request('GET', '/foooooooo');
        $router = new Router([['GET', '/foo', 'Bar@baz']]);

        // Router will ask the factory to build the Error Controller
        $factory = m::mock(ControllerFactory::class);
        $factory->shouldReceive('build')
                ->with('Errors', 'not_found', [])->once()
                ->andReturn(m::mock(
                    Controller::class,
                    ['not_found', []]
                ));

        // Let's call our router
        $router->setControllerFactory($factory);
        $controller = $router->getControllerFor($request);

        $this->assertInstanceOf(ControllerInterface::class, $controller);
        $this->assertInstanceOf(MiddlewareInterface::class, $controller);
    }

    public function test_Router_calls_partial_route_name()
    {
        echo 'Router: should call Error controller @ not_found action for a route with incomplete name';

        $request = new Request('GET', '/fo');
        $router = new Router([['GET', '/foo', 'Bar@baz']]);

        // Router will ask the factory to build the Error Controller
        $factory = m::mock(ControllerFactory::class);
        $factory->shouldReceive('build')
                ->with('Errors', 'not_found', [])->once()
                ->andReturn(m::mock(
                    Controller::class,
                    ['not_found', []]
                ));

        // Let's call our router
        $router->setControllerFactory($factory);
        $controller = $router->getControllerFor($request);

        $this->assertInstanceOf(ControllerInterface::class, $controller);
        $this->assertInstanceOf(MiddlewareInterface::class, $controller);
    }

    public function test_Router_calls_invalid_http_verb()
    {
        echo 'Router: should call Error controller @ method_not_allowed action for a wrong HTTP method call';

        $request = new Request('POST', '/foo');
        $router = new Router([['GET',  '/foo', 'Bar@baz']]);

        // Router will ask the factory to build the Error Controller
        $factory = m::mock(ControllerFactory::class);
        $factory->shouldReceive('build')
                ->with('Errors', 'method_not_allowed', [])->once()
                ->andReturn(m::mock(
                    Controller::class,
                    ['method_not_allowed', []]
                ));

        // Let's call our router
        $router->setControllerFactory($factory);
        $controller = $router->getControllerFor($request);

        $this->assertInstanceOf(ControllerInterface::class, $controller);
        $this->assertInstanceOf(MiddlewareInterface::class, $controller);
    }
}
