<?php

namespace Recipeland\Controllers;

use Recipeland\Http\Response;

abstract class Controller
{
    protected $response;
    
    
    public function __construct() 
    {
        $this->response = new Response;
    }
    
    public function __call($method, $parameters)
    {
        throw new BadMethodCallException("Method [$method] does not exist.");
    }
    
    protected function render()
    {
        $this->response->send();
    }
    
}
