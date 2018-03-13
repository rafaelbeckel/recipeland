<?php

namespace Recipeland\Traits;

use InvalidArgumentException;
use Recipeland\Helpers\Validator;

trait ParsesValidationDSLTrait
{
    private function parseRule(string $rule): array
    {
        if (strpos($rule, ':')) {
            [$modifier, $rulefunction] = explode(':', $rule, 2);

            $value = $this->runModifier($modifier, $rulefunction);

            [$rulename, $arguments] = $this->parseFunction($rulefunction);
            $ruleClass = $this->toCamelCase($rulename);

            if (empty($value)) {
                throw new InvalidArgumentException('Invalid Rule Format.');
            }
        } else {
            $value = $this->payload;
            $ruleClass = $this->toCamelCase($rule);
            $arguments = [];
        }

        return [$ruleClass, $value, $arguments];
    }

    private function runModifier(string $modifier, string $rulefunction)
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

    private function forEach(iterable $payload, string $rule): bool
    {
        foreach ($payload as $value) {
            $validator = new class($this->ruleFactory) extends Validator {
            };

            $validator->addRule(substr($rule, 5)); // Without "each:"

            if (!$validator->validate($value)) {
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

    private function count()
    {
        return count((array) $this->payload);
    }

    private function item($key)
    {
        return $this->payload[$key];
    }

    private function toCamelCase(string $string): string
    {
        return str_replace('_', '', ucwords($string, '_'));
    }
}
