<?php

declare(strict_types=1);

namespace Recipeland\Interfaces;

use Recipeland\Interfaces\RequestInterface as RecipelandRequest;
use Psr\Http\Message\ServerRequestInterface as Request;

interface SpecializedRequestInterface extends RecipelandRequest
{
    public function addRules();
    
    public function addRule($rule);
    
    public function getValidator();
    
    public function validate(): bool;
    
    public static function upgradeIfValid(Request $request): Request;
}
