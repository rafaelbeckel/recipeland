<?php

declare(strict_types=1);

namespace Recipeland\Helpers;

use Recipeland\Traits\ParsesValidationDSLTrait;
use Recipeland\Interfaces\ValidatorInterface;
use Recipeland\Interfaces\FactoryInterface;
use Recipeland\Helpers\Rules\RuleFactory;

abstract class Validator implements ValidatorInterface
{
    use ParsesValidationDSLTrait;

    protected $rules = [];

    protected $ruleFactory;

    protected $payload;

    protected $message = '';

    final public function __construct(FactoryInterface $factory = null)
    {
        if ($factory) {
            $this->ruleFactory = $factory;
        } else {
            $this->ruleFactory = new RuleFactory();
        }

        $this->init();
    }

    protected function init(): void
    {
        /*
         * Child classes should add rules here,
         * by calling addRule() with our mini-DSL.
         *
         * Examples:
         *
         * $this->addRule('rule_name');
         * $this->addRule('each:rule_name');
         * $this->addRule('modifier:rule_name');
         * $this->addRule('each:modifier:rule_name');
         * $this->addRule('rule_name(arg1, arg2, ...)');
         * $this->addRule('each:rule_name(arg1, arg2, ...)');
         * $this->addRule('modifier:rule_name(arg1, arg2, ...)');
         * $this->addRule('each:modifier:rule_name(arg1, arg2, ...)');
         * $this->addRule('modifier(arg1, arg2, ...):rule_name(arg1, arg2, ...)');
         * $this->addRule('each:modifier(arg1, arg2, ...):rule_name(arg1, arg2, ...)');
         *
         * Rules will be applied on validate(payload): bool.
         * The 'rule_name' will map to a class RuleName(payload),
         * then the Validator will call RuleName->apply(arg1, arg2, ...).
         *
         * Modifiers:
         * If a modifier is provided, we'll call a local function with the same name,
         * and then we'll pass its results to the Rule class, instead of the Payload.
         *
         *    Available modifiers:
         *    'each'
         *    'count'
         *    'item(index)'
         *
         *    It's possible to implement custom modifiers by
         *    defining local methods in the child classes.
         *
         * Each:
         * If the special modifier 'each' is provided, and the Payload is an iterable,
         * we will create a new instance of the Validator, add only the current Rule to it,
         * and call it for each element of the Payload. It's possible to validate nested arrays
         * using chained 'each:' modifiers in a command, like this: 'each:each:modifier:rule_name'.
         */
    }

    final public function addRule(string $rule): ValidatorInterface
    {
        $this->rules[] = $rule;

        return $this;
    }

    public function validate($payload): bool
    {
        $this->payload = $payload;

        foreach ($this->rules as $rule) {
            [$ruleClass, $value, $arguments] = $this->parseRule($rule);

            if ($value == 'each') {
                if (!$this->forEach($payload, $rule)) {
                    return false;
                }
            } else {
                $ruleObject = $this->ruleFactory->build($ruleClass, $value);

                // Apply the current rule
                if (!call_user_func_array([$ruleObject, 'apply'], $arguments)) {
                    $this->message = $ruleObject->getMessage();

                    return false;
                }
            }
        }

        return true;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
