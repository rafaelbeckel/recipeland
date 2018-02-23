<?php

namespace Recipeland\Http;

use PHPUnit\Framework\TestCase;
use Recipeland\Http\Request;

class RequestTest extends TestCase
{
    
    public function test_start_method_returns_Request_instance()
    {
        $request = Request::start();
        $this->assertInstanceOf(Request::class, $request);
    }
    
}