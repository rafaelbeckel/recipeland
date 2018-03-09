<?php declare(strict_types=1);

namespace Recipeland\Helpers\Validators;

use \InvalidArgumentException;
use Recipeland\Helpers\Validator;

class RoutesArrayValidator extends Validator
{
    private $HTTP_Methods = ['HEAD', 'GET', 'POST', 'PUT', 
                             'PATCH', 'DELETE', 'OPTIONS', 
                             'PURGE', 'TRACE', 'CONNECT'];
    
    public function validate($routes): void
    {
        
        $this->not_empty($routes);
        
        $this->must_be_array($routes);
        
        
        
        
        
        
        
        
        foreach ($routes as $route) {
            $this->validateRoute($route);
        }
        
    }
    
    protected function not_empty($value)
    {
        if (empty($value)) {
            throw new InvalidArgumentException(self::EMPTY_ARRAY);
        }
    }
    
    protected function validateRoute(array $route): void
    {
        
        $this->is_between(count($route), 3, 4);
        
        // if (! $this->between(count($route), 3, 4)) {
        //     throw new InvalidArgumentException(self::INVALID_ELEMENT_COUNT);
        // }
        
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
