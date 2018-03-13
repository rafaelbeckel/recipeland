<?php

namespace Recipeland\Helpers\Rules;

use Recipeland\Interfaces\RuleInterface;

abstract class AbstractRule implements RuleInterface
{
    protected $message;

    protected $value;

    final public function __construct($value)
    {
        $this->value = $value;
    }

    abstract public function apply(...$arguments): bool;

    public function getMessage(): string
    {
        return $this->message;
    }
}
