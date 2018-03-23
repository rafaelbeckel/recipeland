<?php declare(strict_types=1);

namespace Recipeland\Http\Request;

use Recipeland\Http\Request;
use InvalidArgumentException;
use Recipeland\Helpers\Rules\RuleFactory;
use Recipeland\Helpers\Validators\RequestValidator;
use Recipeland\Traits\ImplementsSpecializedRequest;
use Recipeland\Interfaces\SpecializedRequestInterface;
use Recipeland\Interfaces\ValidatorInterface as Validator;
use Psr\Http\Message\ServerRequestInterface as ServerRequest;

abstract class SpecializedRequest extends Request implements SpecializedRequestInterface
{
    protected $validator;
    
    protected $data;
    
    abstract public function addRules();
    
    public function addRule($rule)
    {
        $scope = strtok($rule, ':');
        $scope = ltrim($scope, '?');
        
        if (array_key_exists($scope, $this->data)) {
            $this->validator = $this->getValidator();
            $this->validator->addRule($rule);
        } else {
            throw new InvalidArgumentException(
                'Rule "'.$rule.'" does not contain a valid scope for validating a Request.'
            );
        }
    }
    
    public function validate(): bool
    {
        $this->validator = $this->getValidator();
        
        foreach ($this->data as $key => $value) {
            if (! $this->validator->validate($value, $key)) {
                return false;
            }
        }
        return true;
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
            return $request->withAttribute(
                'message',
                $specialized->getValidator()->getMessage()
            );
        }
    }
}
