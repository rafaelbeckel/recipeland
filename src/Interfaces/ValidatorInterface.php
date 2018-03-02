<?php

namespace Recipeland\Interfaces;

interface ValidatorInterface
{
    public function validate($payload): void;
}