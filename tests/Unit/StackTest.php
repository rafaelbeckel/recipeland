<?php

namespace Tests\Unit;

use GuzzleHttp\Psr7\ServerRequest as Request;
use Recipeland\Interfaces\FactoryInterface;
use Recipeland\Interfaces\StackInterface;
use Psr\Http\Server\MiddlewareInterface;
use Recipeland\Controllers\Errors;
use InvalidArgumentException;
use GuzzleHttp\Psr7\Response;
use Recipeland\Stack;
use Tests\TestSuite;

class StackTest extends TestSuite
{
    protected $stack;
    protected $f;

    public function setUp()
    {
        parent::setUp();

        $this->f = $this->createMock(FactoryInterface::class);

        $this->stack = new class($this->f) extends Stack {
            protected $items = ['foo', 'bar', 'baz'];
        };
    }

    public function test_interface()
    {
        echo 'Stack: test if abstract parent class implements StackInterface';
        $this->assertInstanceOf(StackInterface::class, $this->stack);
    }

    public function test_constructor()
    {
        echo 'Stack: constructor should replace the entire list if receive an argument';

        $expected = ['fizz', 'buzz'];

        $stack = new class($this->f, ['fizz', 'buzz']) extends Stack {
            protected $items = ['foo', 'bar', 'baz'];
        };

        $this->assertEquals($expected, $stack->getAll());
    }

    public function test_getAll()
    {
        echo 'Stack: getAll() returns all items into the stack';

        $expected = ['foo', 'bar', 'baz'];

        $this->assertEquals($expected, $this->stack->getAll());
    }

    public function test_append()
    {
        echo 'Stack: append() appends item to the end of the stack';

        $expected = ['foo', 'bar', 'baz', 'waldo'];
        $this->stack->append('waldo');

        $this->assertEquals($expected, $this->stack->getAll());
    }

    public function test_prepend()
    {
        echo 'Stack: prepend() prepends item in the beggining of the stack';

        $expected = ['waldo', 'foo', 'bar', 'baz'];
        $this->stack->prepend('waldo');

        $this->assertEquals($expected, $this->stack->getAll());
    }

    public function test_shift()
    {
        echo 'Stack: shift() removes first item of the stack';

        $expected = ['bar', 'baz'];
        $this->stack->shift();

        $this->assertEquals($expected, $this->stack->getAll());
    }

    public function test_pop()
    {
        echo 'Stack: pop() removes last item of the stack';

        $expected = ['foo', 'bar'];
        $this->stack->pop();

        $this->assertEquals($expected, $this->stack->getAll());
    }

    public function test_pointers()
    {
        echo 'Stack: test methods for moving and getting the array pointer';

        // forward
        $this->assertEquals('foo', $this->stack->getCurrentItem());
        $this->stack->movePointerToNextItem();
        $this->assertEquals('bar', $this->stack->getCurrentItem());
        $this->stack->movePointerToNextItem();
        $this->assertEquals('baz', $this->stack->getCurrentItem());
        $this->stack->movePointerToNextItem();
        $this->assertEquals(false, $this->stack->getCurrentItem());

        // reset
        $this->stack->resetPointerToFirstItem();
        $this->assertEquals('foo', $this->stack->getCurrentItem());

        // backwards
        $this->stack->movePointerToLastItem();
        $this->assertEquals('baz', $this->stack->getCurrentItem());
        $this->stack->movePointerToPreviousItem();
        $this->assertEquals('bar', $this->stack->getCurrentItem());
        $this->stack->movePointerToPreviousItem();
        $this->assertEquals('foo', $this->stack->getCurrentItem());
        $this->stack->movePointerToPreviousItem();
        $this->assertEquals(false, $this->stack->getCurrentItem());
    }

    public function test_handle()
    {
        echo 'Stack: test handle(request) calls Middleware and returns Response object';

        $request = new Request('GET', '/');
        $middleware = $this->createMock(MiddlewareInterface::class);

        $stack = new class($this->f, [$middleware]) extends Stack {
        };

        $middleware->expects($this->once())
                   ->method('process')
                   ->with($request, $stack)
                   ->willReturn($this->createMock(Response::class));

        $response = $stack->handle($request);

        $this->f->expects($this->never())
                ->method('build');
    }
    
    public function test_handle_middleware_without_response()
    {
        echo 'Stack: test handle(request) calls Middleware without response';

        $request = new Request('GET', '/');
        $middleware = $this->createMock(Errors::class);

        $stack = new class($this->f, [Errors::class, null]) extends Stack {
        };
                   
        $this->f->expects($this->once())
                ->method('build')
                ->with(Errors::class)
                ->willReturn($middleware);
        
        $response = $stack->handle($request);
    }
    
    public function test_handle_non_middleware_item()
    {
        echo 'Stack: test handle(request) calls with wrong item listed';

        $this->expectException(InvalidArgumentException::class);

        $request = new Request('GET', '/');
        $stack = new class($this->f, ['not_a_middleware']) extends Stack {
        };
        $response = $stack->handle($request);
    }

    public function test_last()
    {
        $beer = "\u{1F37A} ";
        echo 'ALL TESTS FINISHED! '.$beer.$beer.$beer;
        $this->assertTrue(true);
    }
}
