<?php declare(strict_types=1);

namespace Recipeland\Http\Request;

use Recipeland\Http\Request;
use Recipeland\Helpers\Rules\RuleFactory;
use Recipeland\Helpers\Validators\RequestValidator;
use Recipeland\Traits\ImplementsSpecializedRequest;
use Recipeland\Interfaces\SpecializedRequestInterface;
use Recipeland\Interfaces\ValidatorInterface as Validator;
use Psr\Http\Message\ServerRequestInterface as ServerRequest;

abstract class SpecializedRequest extends Request implements SpecializedRequestInterface
{
    protected $valid = false;
    protected $validator;
    
    abstract public function addRules();
    
    public function addRule($rule)
    {
        $this->validator = $this->getValidator();
        $this->validator->addRule($rule);
    }
    
    public function validate(): bool
    {
        return $this->getValidator()->validate($this->getQueryParams());
    }
    
    public function getValidator()
    {
        return $this->validator ?: new RequestValidator(new RuleFactory());
    }
    
    public static function upgradeIfValid(ServerRequest $request): ServerRequest
    {
        $specialized = static::upgrade($request);
        $specialized->addRules();
        
        if ($specialized->validate()) {
            return $specialized;
        } else {
            return $request;
        }
    }
}
