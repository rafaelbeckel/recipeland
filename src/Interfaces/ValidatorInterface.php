<?php declare(strict_types=1);

namespace Recipeland\Interfaces;

interface ValidatorInterface
{
    public function validate($payload): void;
}
