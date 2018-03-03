<?php

namespace Recipeland\Helpers\Validators;

use \InvalidArgumentException;
use Recipeland\Helpers\Validator;

class RoutesArrayValidator extends Validator
{
    //@TODO pull from lang file
    const EMPTY_ARRAY = "Route collection array cannot be empty";
    const ERROR_CONTROLLER_NOT_FOUND = "Error controller not found";
    const INVALID_ELEMENT_COUNT = "Route array must have 3 elements";
    const FIRST_ELEMENT_MUST_BE_REQUEST_METHOD = "First element of routes array must be Request Method ('GET', 'POST', etc.)";
    const SECOND_ELEMENT_MUST_BE_URL_PATH = "Second element of routes array must be URL Path";
    const THIRD_ELEMENT_MUST_BE_CONTROLLER_AND_ACTION = "Third element of routes array must be in the format Controller@action";
    const URL_PATH_PATTERN = "|(\/)([\w\/\[\]\{\}]*)(\??[\w\/\[\]\{\}]+\=[\w\/\[\]\{\}]+)*(\&?[\w\/\[\]\{\}]+\=[\w\/\[\]\{\}]+)*|i";
    const AT_PATTERN = "|[^@]*@[^@]*|";
    
    private $HTTP_Methods = [
        'HEAD',
        'GET',
        'POST',
        'PUT',
        'PATCH',
        'DELETE',
        'PURGE',
        'OPTIONS',
        'TRACE',
        'CONNECT',
    ];
    
    
    public function validate($routes): void
    {
        $this->validateRoutesArray($routes);
    }
    
    private function validateRoutesArray(array $routes): void
    {
        if (empty($routes)) {
            throw new InvalidArgumentException(self::EMPTY_ARRAY);
        }
        
        foreach ($routes as $route) {
            $this->validateRoute($route);
        }
    }
    
    private function validateRoute(array $route): void
    {
        if (count($route) !== 3) {
            throw new InvalidArgumentException(self::INVALID_ELEMENT_COUNT);
        }
        
        if (! in_array($route[0], $this->HTTP_Methods)) {
            throw new InvalidArgumentException(self::FIRST_ELEMENT_MUST_BE_REQUEST_METHOD);
        }
            
        if (! preg_match(self::URL_PATH_PATTERN, $route[1])) {
            throw new InvalidArgumentException(self::SECOND_ELEMENT_MUST_BE_URL_PATH);
        }
            
        if (! preg_match(self::AT_PATTERN, $route[2])) {
            throw new InvalidArgumentException(self::THIRD_ELEMENT_MUST_BE_CONTROLLER_AND_ACTION);
        }
    }
}
