<?php

declare(strict_types=1);

namespace Recipeland\Interfaces;

interface StackInterface
{
    public function __construct(FactoryInterface $factory, array $items = null);

    public function getAll();

    public function append($item);

    public function prepend($item);

    public function shift();

    public function pop();

    public function resetPointerToFirstItem();

    public function movePointerToLastItem();

    public function getCurrentItem();

    public function movePointerToNextItem();

    public function movePointerToPreviousItem();
}
