<?php

namespace Recipeland;

use GuzzleHttp\Psr7\ServerRequest as Request;
use Recipeland\Interfaces\StackInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Recipeland\Stack;
use Tests\TestSuite;
use \Mockery as m;

class StackTest extends TestSuite
{
    protected $stack;
    
    public function setUp()
    {
        parent::setUp();
        
        $this->stack = new class extends Stack {
            
            protected $items = ['foo', 'bar', 'baz'];
            
        };
    }
    
    public function test_interface()
    {
        echo "Stack: test if abstract parent class implements StackInterface";
        $this->assertInstanceOf(StackInterface::class, $this->stack);
    }
    
    public function test_constructor()
    {
        echo "Stack: constructor should replace the entire list if receive an argument";
        
        $expected =  ['fizz', 'buzz'];
        
        $stack = new class(['fizz', 'buzz']) extends Stack {
            protected $items = ['foo', 'bar', 'baz'];
        };
        
        $this->assertEquals($expected, $stack->getAll());
    }
    
    public function test_getAll()
    {
        echo "Stack: getAll() returns all items into the stack";
        
        $expected =  ['foo', 'bar', 'baz'];
        
        $this->assertEquals($expected, $this->stack->getAll());
    }
    
    public function test_append()
    {
        echo "Stack: append() appends item to the end of the stack";
        
        $expected =  ['foo', 'bar', 'baz', 'waldo'];
        $this->stack->append('waldo');
        
        $this->assertEquals($expected, $this->stack->getAll());
    }
    
    public function test_prepend()
    {
        echo "Stack: prepend() prepends item in the beggining of the stack";
        
        $expected =  ['waldo', 'foo', 'bar', 'baz'];
        $this->stack->prepend('waldo');
        
        $this->assertEquals($expected, $this->stack->getAll());
    }
    
    public function test_shift()
    {
        echo "Stack: shift() removes first item of the stack";
        
        $expected =  ['bar', 'baz'];
        $this->stack->shift();
        
        $this->assertEquals($expected, $this->stack->getAll());
    }
    
    public function test_pop()
    {
        echo "Stack: pop() removes last item of the stack";
        
        $expected =  ['foo', 'bar'];
        $this->stack->pop();
        
        $this->assertEquals($expected, $this->stack->getAll());
    }
    
    public function test_pointers()
    {
        echo "Stack: test methods for moving and getting the array pointer";
        
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
        echo "Stack: test handle(request) calls Middleware and returns Response object";
        
        $request = new Request('GET','/');
        $middleware = m::spy(MiddlewareInterface::class);
        $stack = new class([$middleware]) extends Stack {};
        
        $response = $stack->handle($request);
        
        $middleware->shouldHaveReceived('process')->with($request, $stack)->once();
        
        // Ugly workaround for Mockery BUG #785
        $this->addToAssertionCount(1);
    }
    
    public function test_last () {
        // Just a spacer for my fancy custom output.
        $this->assertTrue(true);
    }
    
    
    
    // public function handle(RequestInterface $request): ResponseInterface
    // {
    //     $current = $this->getCurrentItem();
        
    //     if ($current === false) { // Last item
    //         return new Response();
    //     }
        
    //     $middleware = $this->getInstanceOf($current);
        
    //     $this->movePointerToNextItem();
        
    //     return $middleware->process($request, $this);
    // }
    
    
    
    
}