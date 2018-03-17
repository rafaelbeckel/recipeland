<?php declare(strict_types=1);

namespace Recipeland\Http\Request;

use GuzzleHttp\Psr7\ServerRequest;

class CreateRecipeRequest extends ServerRequest implements ValidatorInterface
{
    public function required()
    {
    }
    
    public function optional()
    {
    }
    
    public function validate(ServerRequest $request): bool
    {
    }
}
