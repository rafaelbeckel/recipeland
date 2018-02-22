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
    
    /**
     * Handle calls to missing methods on the controller.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     *
     * @throws \BadMethodCallException
     */
    public function __call($method, $parameters)
    {
        throw new BadMethodCallException("Method [$method] does not exist.");
    }
    
    protected function render()
    {
        $this->response->send();
    }
    
    static public function getNamespace()
    {
        return __NAMESPACE__;
    }
    
}
