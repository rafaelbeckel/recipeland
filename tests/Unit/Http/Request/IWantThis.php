<?php 

declare(strict_types=1);

namespace Tests\Unit\Http\Request;

use Recipeland\Http\Request\SpecializedRequest;

class IWantThis extends SpecializedRequest
{
    public function addRules(): void
    {
        $this->addRule('item(foo):not_empty');
    }
}
