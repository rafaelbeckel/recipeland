<?php

namespace Recipeland\Traits;

use InvalidArgumentException;
use Recipeland\Helpers\Validator;

trait ParsesValidationDSL
{
    private function parseRule(string $rule): array
    {
        if (strpos($rule, ':')) {
            [$modifier, $rulefunction] = explode(':', $rule, 2);

            $value = $this->runModifier($modifier, $rulefunction);

            [$rulename, $arguments] = $this->parseFunction($rulefunction);
            $ruleClass = $this->toCamelCase($rulename);

            if (empty($value)) {
                throw new InvalidArgumentException('Invalid Rule Format or Item not Found.');
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

    private function each(iterable $payload, string $rule): bool
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

    private function count(): int
    {
        return count((array) $this->payload);
    }

    private function item($key)
    {
        if (is_array($this->payload)) {
            return $this->payload[$key] ?? null;
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
