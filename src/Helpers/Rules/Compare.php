<?php

declare(strict_types=1);

namespace Recipeland\Helpers\Rules;

use BadMethodCallException;

class Compare extends AbstractRule
{
    protected $message = 'errors.validation.$1_$2_$3';

    public function apply(...$arguments): bool
    {
        if (count($arguments) != 3) {
            throw new BadMethodCallException('Compare needs exactly 3 arguments: key1, boolean_operator, key 2');
        }
        
        $key1 = $arguments[0];
        $operator = $arguments[1];
        $key2 = $arguments[2];
        
        $this->message = str_replace(['$1', '$2', '$3'], [$key1,$operator,$key2], $this->message);
        
        return $this->compare($key1, $operator, $key2);
    }

    private function compare($key1, string $operator, $key2)
    {
        if (is_array($this->value) &&
            isset($this->value[$key1]) &&
            isset($this->value[$key2]) &&
            is_numeric($this->value[$key1]) &&
            is_numeric($this->value[$key2])
        ) {
            $value1 = $this->value[$key1];
            $value2 = $this->value[$key2];
        } else {
            return false;
        }
        
        switch ($operator) {
            case '>':
                $condition = floatval($value1) > floatval($value2);
                break;
            case '<':
                $condition = floatval($value1) < floatval($value2);
                break;
            case '>=':
                $condition = floatval($value1) >= floatval($value2);
                break;
            case '<=':
                $condition = floatval($value1) <= floatval($value2);
                break;
            case '==':
                $condition = floatval($value1) == floatval($value2);
                break;
            case '===':
                $condition = floatval($value1) === floatval($value2);
                break;
            case '!=':
                $condition = floatval($value1) != floatval($value2);
                break;
            case '!==':
                $condition = floatval($value1) !== floatval($value2);
                break;
        }
        
        return $condition;
    }
}
