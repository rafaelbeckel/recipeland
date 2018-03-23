<?php

namespace Recipeland\Traits;

use BadMethodCallException;
use InvalidArgumentException;
use Recipeland\Helpers\Validator;

trait ParsesValidationDSL
{
    public function __call($method, $parameters)
    {
        throw new BadMethodCallException('Modifier ['.$method.'] does not exist.');
    }
    
    private function applyRules($payload, array $rules, ?string $base_rule):bool
    {
        foreach ($rules as $rule) {
            $full_rule = $rule;
            
            if ($base_rule) {
                $optional = $this->isOptional($base_rule);
            } else {
                $optional = $this->isOptional($rule);
            }
            
            $rule = ltrim($rule, '?');
            
            if ($this->scope) {
                if (strpos($rule, $this->scope.':') !== false) {
                    $rule = str_replace($this->scope.':', '', $rule);
                } else {
                    continue;
                }
            }
            
            [$ruleName, $value, $arguments] = $this->parseRule($rule);
            
            if ($value == 'each') {
                $rule = substr($rule, 5); // without 'each:'
                $base_rule = $base_rule ?: $full_rule;
                if (!is_iterable($payload) || !$this->each($payload, $rule, $base_rule)) {
                    return false;
                }
            } elseif (strpos($ruleName, ':')) { // "value" is a item(n)
                $base_rule = $base_rule ?: $full_rule;
                if (!$this->each([$value], $ruleName, $base_rule)) {
                    return false;
                }
            } elseif (!is_null($value)) {
                $ruleObject = $this->ruleFactory->build($ruleName, $value);
                
                // Apply the current rule
                if (!call_user_func_array([$ruleObject, 'apply'], $arguments)) {
                    $context = $base_rule ? $base_rule.' -> ' : '';
                    $this->message = $context.$ruleObject->getMessage();
                    
                    return false;
                }
            } elseif (!$optional) {
                $context = $base_rule ? $base_rule.' -> ' : '';
                $this->message = $context.'Mandatory rule is null or malformed.';
                
                return false;
            }
            
            $base_rule = null;
        }
        
        return true;
    }
    
    private function isOptional(string $rule)
    {
        return $rule[0] == '?';
    }
    
    private function parseRule(string $rule): array
    {
        if (strpos($rule, ':')) {
            [$modifier, $rulefunction] = explode(':', $rule, 2);

            $value = $this->runModifier($modifier);

            [$rulename, $arguments] = $this->parseFunction($rulefunction);
            
            if (strpos($rulename, ':')) {
                $ruleClass = $rulefunction;
            } else {
                $ruleClass = $this->toCamelCase($rulename);
            }
        } else {
            $value = $this->payload;
            $ruleClass = $this->toCamelCase($rule);
            $arguments = [];
        }

        return [$ruleClass, $value, $arguments];
    }

    private function runModifier(string $modifier)
    {
        [$method, $params] = $this->parseFunction($modifier);
        
        if ($method == 'each') {
            return $method;
        } elseif (method_exists($this, $method)) {
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
            [$functionName, $arguments] = explode('(', $function, 2);
            $arguments = explode(',', rtrim($arguments, ')'));
        } else {
            $functionName = $function;
            $arguments = [];
        }

        return [$functionName, $arguments];
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
}
