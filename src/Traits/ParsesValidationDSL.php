<?php

namespace Recipeland\Traits;

use BadMethodCallException;
use InvalidArgumentException;
use Recipeland\Helpers\Validator;

/**
 * Implements the Domain Specific Language for the Validator.
 * Can be used by any class that implemets ValidatorInterface.
 *
 *
 * 
 */
trait ParsesValidationDSL
{
    private $optional = false;
    private $base_rule = null;
    
    public function __call($method, $parameters)
    {
        throw new BadMethodCallException('Modifier ['.$method.'] does not exist.');
    }
    
    private function applyRules($payload, array $rules, ?string $base_rule):bool
    {
        foreach ($rules as $rule) {
            $this->base_rule = $base_rule ?: $rule;
            
            [$rule_name, $value, $arguments] = $this->parseRule($rule);
            
            if ($rule_name == 'continue') {
                continue;

            } elseif ($rule_name == 'each') {
                $rule = substr($rule, 5); // drop 'each:'
                if (!is_iterable($payload) ||
                    !$this->each($payload, $rule, $this->base_rule)
                ) {
                    return false;
                }

            } elseif (strpos($rule_name, ':')) {
                if (!$this->each([$value], $rule_name, $this->base_rule)) {
                    return false;
                }

            } elseif (!is_null($value)) {
                if (!$this->applyRule($rule_name, $value, $arguments)) {
                    return false;
                }
                
            } elseif (!$this->optional) {
                $this->message = $this->base_rule.' -> Mandatory rule is null.';
                return false;
            }
            
            $base_rule = null;
            $this->base_rule = null;
        }
        
        return true;
    }
    
    private function parseRule(string $rule): array
    {
        $this->optional = $this->base_rule[0] == '?';
        $rule = ltrim($rule, '?');
        
        if ($this->scope) {
            if (strpos($rule, $this->scope.':') !== false) {
                $rule = str_replace($this->scope.':', '', $rule);
            } else {
                return ['continue', null, null]; // ignore rule out of scope
            }
        }
        
        if (strpos($rule, ':')) {
            [$modifier, $rule_function] = explode(':', $rule, 2);
            
            if ($modifier == 'each') {
                return ['each', null, null]; // call this class recursively
            }
            
            $value = $this->runModifier($modifier);
            
            if (is_null($value) && $this->optional) {
                return ['continue', null, null]; // ignore optional null value
            }
            
            [$rule_name, $arguments] = $this->parseFunction($rule_function);
            
            if (!strpos($rule_name, ':')) {
                $rule_name = $this->toCamelCase($rule_name); // it is THE rule
            }
        } else {
            $rule_name = $this->toCamelCase($rule) ;
            $value = $this->payload;
            $arguments = [];
        }

        return [$rule_name, $value, $arguments];
    }
    
    private function applyRule(string $rule_name, $value, array $arguments)
    {
        if ($this->canApply($rule_name, $value, $arguments)) {
            $rule = $this->ruleFactory->build($rule_name, $value);
            if (!call_user_func_array([$rule, 'apply'], $arguments)) {
                $this->removeClassNameFromRule();
                $this->message = $this->base_rule.' -> '.$rule->getMessage();
                
                return false;
            }
        }
        return true;
    }
    
    private function canApply(string $rule_name, $value, array $arguments)
    {
        if ($this->optional && $rule_name == 'Compare') {
            $key1 = $arguments[0] ?? 0;
            $key2 = $arguments[2] ?? 1;
             
            if (!is_array($value) ||
                !array_key_exists($key1, $value) ||
                !array_key_exists($key2, $value)
            ) {
                return false;
            }
        }
        
        return true;
    }

    private function runModifier(string $modifier)
    {
        [$method, $params] = $this->parseFunction($modifier);
        
        if (method_exists($this, $method)) {
            return call_user_func_array([$this, $method], $params);
        } else {
            throw new InvalidArgumentException('Modifier method "'.$method.'" not found.');
        }
    }

    private function each(iterable $payload, string $rule, string $base_rule): bool
    {
        foreach ($payload as $value) {
            $validator = new class($this->ruleFactory) extends Validator {
            };

            $validator->addRule($rule);

            if (!$validator->validate($value, null, $base_rule)) {
                $this->message = $validator->getMessage();
                return false;
            }
        }

        return true;
    }

    private function parseFunction(string $function)
    {
        if (strpos($function, '(') && !strpos($function, ':')) {
            if (')' != substr($function, -1)) {
                throw new InvalidArgumentException('Missing ")" in function "'.$function.'"');
            }
            [$function_name, $arguments] = explode('(', $function, 2);
            $arguments = explode(',', rtrim($arguments, ')'));
        } else {
            $function_name = $function;
            $arguments = [];
        }
        
        return [$function_name, $arguments];
    }

    private function count(): int
    {
        return count((array) $this->payload);
    }

    private function item($key)
    {
        if (is_array($this->payload) && !empty($this->payload[$key])) {
            return $this->payload[$key];
        } elseif (
            (is_string($this->payload) || is_numeric($this->payload)) &&
            is_numeric($key) &&
            intval($key) >= 0 &&
            intval($key) < strlen((string) $this->payload)
        ) {
            return ((string) $this->payload)[intval($key)];
        } else {
            return null;
        }
    }
    
    private function chars(): ?int
    {
        if (is_string($this->payload) || is_numeric($this->payload)) {
            return strlen((string) $this->payload);
        } else {
            return null;
        }
    }

    private function toCamelCase(string $string): string
    {
        return str_replace('_', '', ucwords($string, '_'));
    }

    private function removeClassNameFromRule(): string
    {
        if (strpos($this->base_rule, 'instance_of') !== false) {
            $this->base_rule = substr($this->base_rule,0,strrpos($this->base_rule,'instance_of'));
            $this->base_rule .= 'instance_of()';
        }
    }
}
