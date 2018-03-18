<?php

namespace Tests\Unit\Http;

use Recipeland\Controllers\AbstractController as Controller;
use Recipeland\Interfaces\ControllerInterface;
use GuzzleHttp\Psr7\ServerRequest as Request;
use Recipeland\Interfaces\ValidatorInterface;
use Recipeland\Interfaces\FactoryInterface;
use Psr\Http\Server\MiddlewareInterface;
use Recipeland\Http\Router;
use Tests\TestSuite;

class RouterTest extends TestSuite
{
    protected $v;

    public function setUp()
    {
        parent::setUp();
        $this->v = $this->createMock(ValidatorInterface::class);
        $this->v->method('validate')->willReturn(true);
    }

    public function test_Router_controller_and_action()
    {
        echo 'Router: should return the right controller and action from a routes Array and a Request';

        // Router will ask the factory to build the correct Controller
        $factory = $this->createMock(FactoryInterface::class);
        $factory->expects($this->once())
                ->method('build')
                ->with('Bar', 'baz', [])
                ->willReturn($this->createMock(Controller::class));

        $request = new Request('GET', '/foo');
        $router = new Router([['GET', '/foo', 'Bar.baz']], $factory, $this->v);

        // Let's call our router
        $controller = $router->getControllerFor($request);

        $this->assertInstanceOf(ControllerInterface::class, $controller);
        $this->assertInstanceOf(MiddlewareInterface::class, $controller);
    }

    public function test_Router_controller_and_action_with_arguments()
    {
        echo 'Router: should return the right controller and action, with arguments';

        // Router will ask the factory to build the correct Controller
        $factory = $this->createMock(FactoryInterface::class);
        $factory->expects($this->once())
                ->method('build')
                ->with('Bar', 'baz', ['id' => '1234', 'name' => 'Coffee'])
                ->willReturn($this->createMock(Controller::class));

        $request = new Request('GET', '/foo/1234/make/Coffee');
        $router = new Router([['GET', '/foo/{id}/make/{name}', 'Bar.baz']], $factory, $this->v);

        // Let's call our router
        $router->getControllerFor($request);
    }

    public function test_Router_non_existent_route()
    {
        echo 'Router: should call Error controller . not_found action for a non-existent route';

        // Router will ask the factory to build the Error Controller
        $factory = $this->createMock(FactoryInterface::class);
        $factory->expects($this->once())
                ->method('build')
                ->with('Errors', 'not_found', [])
                ->willReturn($this->createMock(Controller::class));

        $request = new Request('GET', '/foooooooo');
        $router = new Router([['GET', '/foo', 'Bar.baz']], $factory, $this->v);

        // Let's call our router
        $router->getControllerFor($request);
    }

    public function test_Router_create_cached_routes_file()
    {
        echo 'Router: should create the cached routes file if it not exists';

        $cache_file = __DIR__.'/temp_file.php';
        if (file_exists($cache_file)) {
            unlink($cache_file);
        }

        // CALL 1: Router will ask the factory to build the correct Controller
        $factory = $this->createMock(FactoryInterface::class);
        $factory->expects($this->once())
                ->method('build')
                ->with('Bar', 'baz', [])
                ->willReturn($this->createMock(Controller::class));

        // CALL 1: We'll instantiate our router with the optional cache_file argument
        $router = new Router([['GET', '/cached', 'Bar.baz']], $factory, $this->v, $cache_file);
        $request = new Request('GET', '/cached');
        $router->getControllerFor($request);

        // If the cache file argument is present, the router will create it
        $this->assertFileExists($cache_file);
    }

    public function test_Router_not_find_a_cached_route()
    {
        echo 'Router: should not find a non-cached valid route, cache should prevail over the array definition';

        $cache_file = __DIR__.'/temp_file.php';
        $this->assertFileExists($cache_file);

        // CALL 2: Now the router will find only cached routes.
        $factory = $this->createMock(FactoryInterface::class);
        $factory->expects($this->once())
                ->method('build')
                ->with('Errors', 'not_found', [])
                ->willReturn($this->createMock(Controller::class));

        // CALL 2: We'll call our router again, with the cache file. It should NOT find the route.
        $router = new Router([['GET', '/not_cached', 'Baz.bim']], $factory, $this->v, $cache_file);
        $request = new Request('GET', '/not_cached');
        $router->getControllerFor($request);
    }

    public function test_Router_find_a_cached_route()
    {
        echo 'Router: should find a cached route, cache should prevail over the array definition';

        $cache_file = __DIR__.'/temp_file.php';
        $this->assertFileExists($cache_file);

        // CALL 3: It should happily find the cached route.
        $factory = $this->createMock(FactoryInterface::class);
        $factory->expects($this->once())
                ->method('build')
                ->with('Bar', 'baz', [])
                ->willReturn($this->createMock(Controller::class));

        // CALL 3: cache should prevail over the array definition.
        $router = new Router([['GET', '/not_cached', 'Baz.bim']], $factory, $this->v, $cache_file);
        $request = new Request('GET', '/cached');
        $router->getControllerFor($request);
    }

    public function test_Router_find_a_non_cached_route()
    {
        echo 'Router: yet another not cached call, should not use the cache if argument is not provided';

        $cache_file = __DIR__.'/temp_file.php';
        $this->assertFileExists($cache_file);

        // CALL 4: The third call (not cached) will find the new route
        $factory = $this->createMock(FactoryInterface::class);
        $factory->expects($this->once())
                ->method('build')
                ->with('Baz', 'bim', [])
                ->willReturn($this->createMock(Controller::class));

        // Call 4: The file argument is not present, so it will find the route in the array.
        $router = new Router([['GET', '/not_cached', 'Baz.bim']], $factory, $this->v);
        $request = new Request('GET', '/not_cached');
        $router->getControllerFor($request);

        // All done, let's clean up the file.
        unlink($cache_file);
        $this->assertFileNotExists($cache_file);
    }

    public function test_Router_calls_partial_route_name()
    {
        echo 'Router: should call Error controller . not_found action for a route with incomplete name';

        // Router will ask the factory to build the Error Controller
        $factory = $this->createMock(FactoryInterface::class);
        $factory->expects($this->once())
                ->method('build')
                ->with('Errors', 'not_found', [])
                ->willReturn($this->createMock(Controller::class));

        $request = new Request('GET', '/fo');
        $router = new Router([['GET', '/foo', 'Bar.baz']], $factory, $this->v);

        // Let's call our router
        $router->getControllerFor($request);
    }

    public function test_Router_calls_invalid_http_verb()
    {
        echo 'Router: should call Error controller . method_not_allowed action for a wrong HTTP method call';

        // Router will ask the factory to build the Error Controller
        $factory = $this->createMock(FactoryInterface::class);
        $factory->expects($this->once())
                ->method('build')
                ->with('Errors', 'method_not_allowed', [])
                ->willReturn($this->createMock(Controller::class));

        $request = new Request('POST', '/foo');
        $router = new Router([['GET',  '/foo', 'Bar.baz']], $factory, $this->v);

        // Let's call our router
        $router->getControllerFor($request);
    }
}
