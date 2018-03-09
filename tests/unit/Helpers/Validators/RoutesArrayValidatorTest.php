<?php

namespace Tests\Helpers\Validators;

use \InvalidArgumentException;
use Recipeland\Http\Router;
use Tests\TestSuite;
use \TypeError;

class RoutesArrayValidatorTest extends TestSuite
{
    public function test_Route_should_not_accept_non_array_arguments()
    {
        echo "Validator: should only accept arrays";
        
        $arguments = [
            "I am not an array!",
            function () {
            },
            new class {
            },
            12345.678,
            12345,
            true,
            null
        ];
        
        foreach ($arguments as $argument) {
            $this->expectException(TypeError::class);
            $router = new Router($argument);
        }
    }
    
    
    public function test_Route_should_not_accept_empty_array()
    {
        echo "Validator: should not accept empty array";
        
        $this->expectException(InvalidArgumentException::class);
        
        $routes = [];
        
        $router = new Router($routes);
    }
    
    
    public function test_Route_array_validation_missing_string()
    {
        echo "Validator: routes array must be complete";
        
        $this->expectException(InvalidArgumentException::class);
        
        $routes = [
            [ 'GET' , '/foo'            , 'Recipes@get'         ],
            [ 'POST', '/foo'            , 'Recipes@create'      ],
            [ 'PUT' , 'Recipes@update' ]  //Missing string
        ];
        
        $router = new Router($routes);
    }
    
    
    public function test_Route_array_validation_extra_string()
    {
        echo "Validator: routes array should not have extra string";
        
        $this->expectException(InvalidArgumentException::class);
        
        $routes = [
            [ 'GET' , '/foo', 'Recipes@get'    ],
            [ 'POST', '/foo', 'Recipes@create' ],
            [ 'PUT' , '/foo', 'Recipes@update', 'Too Many Strings']
        ];
        
        $router = new Router($routes);
    }
    
    
    public function test_Route_array_validation_first_element()
    {
        echo "Validator: first element must be HTTP method";
        
        $this->expectException(InvalidArgumentException::class);
        
        $routes = [
            [ 'I am not an HTTP Verb', '/foo', 'Recipes@get'],
        ];
        
        $router = new Router($routes);
    }
    
    
    public function test_Route_array_validation_second_element()
    {
        echo "Validator: second element must be URL path";
        
        $this->expectException(InvalidArgumentException::class);
        
        $routes = [
            [ 'GET', '???', 'Recipes@get'],
        ];
        
        $router = new Router($routes);
    }
    
    
    public function test_Route_array_validation_third_element()
    {
        echo "Validator: third element must be Controller@action";
        
        $this->expectException(InvalidArgumentException::class);
        
        $routes = [
            [ 'GET', '/foo', 'No_Symbol'],
        ];
        
        $router = new Router($routes);
    }
}
