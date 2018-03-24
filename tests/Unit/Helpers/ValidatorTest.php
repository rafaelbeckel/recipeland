<?php

namespace Tests\Unit\Helpers;

use Recipeland\Helpers\Rules\AbstractRule;
use Recipeland\Helpers\Validator;
use Recipeland\Helpers\Factory;
use InvalidArgumentException;
use BadMethodCallException;
use Tests\TestSuite;

class ValidatorTest extends TestSuite
{
    public function test_call_the_right_rule_true()
    {
        echo 'Validator - DSL: should call the right rule class - return true';

        $weWillTestYou = 'i_will_return_true';
        $camelCasedRuleName = 'RuleName';

        $rule = $this->createMock(AbstractRule::class, ['i_will_return_true']);
        $factory = $this->createMock(Factory::class);

        $rule->expects($this->once())
             ->method('apply')
             ->willReturn(true);

        $factory->expects($this->once())
                ->method('build')
                ->with($camelCasedRuleName, $weWillTestYou)
                ->willReturn($rule);

        $validator = new class($factory) extends Validator {
            protected function init(): void
            {
                $this->addRule('rule_name');
            }
        };

        $this->assertTrue($validator->validate($weWillTestYou));
    }

    public function test_call_the_right_rule_false()
    {
        echo 'Validator - DSL: should call the right rule class - return false';

        $weWillTestYou = 'i_will_return_false_im_so_falsy';
        $camelCasedRuleName = 'RuleName';

        $rule = $this->createMock(AbstractRule::class, ['i_will_return_false_im_so_falsy']);
        $factory = $this->createMock(Factory::class);

        $rule->expects($this->once())
             ->method('apply')
             ->willReturn(false);

        $rule->expects($this->once())
             ->method('getMessage')
             ->willReturn('You are False!');

        $factory->expects($this->once())
                ->method('build')
                ->with($camelCasedRuleName, $weWillTestYou)
                ->willReturn($rule);

        $validator = new class($factory) extends Validator {
            protected function init(): void
            {
                $this->addRule('rule_name');
            }
        };

        $this->assertFalse($validator->validate($weWillTestYou));
        $this->assertEquals($validator->getMessage(), 'rule_name -> You are False!');
    }
    
    public function test_call_optional_rule_null_value()
    {
        echo 'Validator - DSL: test optional rule with null value - return true';

        $weWillTestYou = null;
        $camelCasedRuleName = 'RuleName';

        $factory = $this->createMock(Factory::class);

        $validator = new class($factory) extends Validator {
            protected function init(): void
            {
                $this->addRule('?rule_name');
            }
        };

        $this->assertTrue($validator->validate($weWillTestYou));
    }
    
    public function test_call_optional_rule_with_some_value()
    {
        echo 'Validator - DSL: test optional rule with a valid value';

        $weWillTestYou = 'i_will_return_true';
        $camelCasedRuleName = 'RuleName';

        $rule = $this->createMock(AbstractRule::class, ['i_will_return_true']);
        $factory = $this->createMock(Factory::class);

        $rule->expects($this->once())
             ->method('apply')
             ->willReturn(true);

        $factory->expects($this->once())
                ->method('build')
                ->with($camelCasedRuleName, $weWillTestYou)
                ->willReturn($rule);

        $validator = new class($factory) extends Validator {
            protected function init(): void
            {
                $this->addRule('?rule_name');
            }
        };

        $this->assertTrue($validator->validate($weWillTestYou));
    }
    
    public function test_call_malformed_rule()
    {
        echo 'Validator - DSL: malformed rule - throws Exception';

        $factory = $this->createMock(Factory::class);

        $validator = new class($factory) extends Validator {
            protected function init(): void
            {
                $this->addRule('rule_name(:malformed');
            }
        };
        
        $this->expectException(InvalidArgumentException::class);

        $this->assertTrue($validator->validate('foo'));
    }
    
    public function test_call_malformed_modifier()
    {
        echo 'Validator - DSL: malformed modifier - throws Exception';

        $factory = $this->createMock(Factory::class);

        $validator = new class($factory) extends Validator {
            protected function init(): void
            {
                $this->addRule('non_existent_modifier:foo');
            }
        };
        
        $this->expectException(InvalidArgumentException::class);

        $this->assertTrue($validator->validate('foo'));
    }
    
    public function test_call_invalid_modifier_directly()
    {
        echo 'Validator - DSL: directly call invalid method - throws Exception';

        $factory = $this->createMock(Factory::class);

        $validator = new class($factory) extends Validator {
            protected function init(): void
            {
                $this->addRule('non_existent_modifier:foo');
            }
        };
        
        $this->expectException(BadMethodCallException::class);

        $validator->some_method('foo');
    }

    public function test_modifier_count()
    {
        echo 'Validator - DSL: "count" modifier with array';

        $weWillTestYou = ['I', 'Have', '4', 'Items'];
        $camelCasedRuleName = 'RuleName';

        $rule = $this->createMock(AbstractRule::class, [4]);
        $factory = $this->createMock(Factory::class);

        $rule->expects($this->once())
             ->method('apply')
             ->willReturn(true);

        $factory->expects($this->once())
                ->method('build')
                ->with($camelCasedRuleName, 4)
                ->willReturn($rule);

        $validator = new class($factory) extends Validator {
            protected function init(): void
            {
                $this->addRule('count:rule_name');
            }
        };

        $this->assertTrue($validator->validate($weWillTestYou));
    }
    
    public function test_modifier_count_non_arrays()
    {
        echo 'Validator - DSL: "count" modifier with non-array - returns 1';

        $weWillTestYou = "I am not an array";
        $camelCasedRuleName = 'RuleName';

        $rule = $this->createMock(AbstractRule::class, [1]);
        $factory = $this->createMock(Factory::class);

        $rule->expects($this->once())
             ->method('apply')
             ->willReturn(true);

        $factory->expects($this->once())
                ->method('build')
                ->with($camelCasedRuleName, 1)
                ->willReturn($rule);

        $validator = new class($factory) extends Validator {
            protected function init(): void
            {
                $this->addRule('count:rule_name');
            }
        };

        $this->assertTrue($validator->validate($weWillTestYou));
    }
    
    public function test_modifier_chars()
    {
        echo 'Validator - DSL: "chars" modifier with string';

        $weWillTestYou = 'I have 16 chars!';
        $camelCasedRuleName = 'RuleName';

        $rule = $this->createMock(AbstractRule::class, [16]);
        $factory = $this->createMock(Factory::class);

        $rule->method('apply')
             ->willReturn(true);

        $factory->method('build')
                ->with($camelCasedRuleName, 16)
                ->willReturn($rule);

        $validator = new class($factory) extends Validator {
            protected function init(): void
            {
                $this->addRule('chars:rule_name');
            }
        };

        $this->assertTrue($validator->validate($weWillTestYou));
    }
    
    public function test_modifier_chars_with_integer()
    {
        echo 'Validator - DSL: "chars" modifier with numeric - counts digits';

        $weWillTestYou = 12345;
        $camelCasedRuleName = 'RuleName';

        $rule = $this->createMock(AbstractRule::class, [5]);
        $factory = $this->createMock(Factory::class);

        $rule->method('apply')
             ->willReturn(true);

        $factory->method('build')
                ->with($camelCasedRuleName, 5)
                ->willReturn($rule);

        $validator = new class($factory) extends Validator {
            protected function init(): void
            {
                $this->addRule('chars:rule_name');
            }
        };

        $this->assertTrue($validator->validate($weWillTestYou));
    }
    
    public function test_modifier_chars_with_array()
    {
        echo 'Validator - DSL: "chars" modifier with array - return null';

        $weWillTestYou = ['I', 'am', 'an', 'Array'];
        $camelCasedRuleName = 'RuleName';

        $rule = $this->createMock(AbstractRule::class, [null]);
        $factory = $this->createMock(Factory::class);

        $rule->method('apply')
             ->willReturn(false);

        $factory->method('build')
                ->with($camelCasedRuleName, null)
                ->willReturn($rule);

        $validator = new class($factory) extends Validator {
            protected function init(): void
            {
                $this->addRule('chars:rule_name');
            }
        };

        $this->assertFalse($validator->validate($weWillTestYou));
    }

    public function test_modifier_item()
    {
        echo 'Validator - DSL: "item(i)" modifier with array';

        $weWillTestYou = ['I', 'Am', 'The', 'Fourth', 'Item'];
        $camelCasedRuleName = 'RuleName';

        $rule = $this->createMock(AbstractRule::class, ['Fourth']);
        $factory = $this->createMock(Factory::class);

        $rule->method('apply')
             ->willReturn(true);

        $factory->method('build')
                ->with($camelCasedRuleName, 'Fourth')
                ->willReturn($rule);

        $validator = new class($factory) extends Validator {
            protected function init(): void
            {
                $this->addRule('item(3):rule_name');
            }
        };

        $this->assertTrue($validator->validate($weWillTestYou));
    }
    
    public function test_modifier_item_string_key()
    {
        echo 'Validator - DSL: "item(i)" modifier with array, with string key';

        $weWillTestYou = ['You'  => 'Will',
                          'Find' => 'Me'];
                          
        $camelCasedRuleName = 'RuleName';

        $rule = $this->createMock(AbstractRule::class, ['Me']);
        $factory = $this->createMock(Factory::class);

        $rule->method('apply')
             ->willReturn(true);

        $factory->method('build')
                ->with($camelCasedRuleName, 'Me')
                ->willReturn($rule);

        $validator = new class($factory) extends Validator {
            protected function init(): void
            {
                $this->addRule('item(Find):rule_name');
            }
        };

        $this->assertTrue($validator->validate($weWillTestYou));
    }
    
    public function test_modifier_item_array_non_existent_item()
    {
        echo 'Validator - DSL: "item(i)" modifier with array - non existent item - return null';

        $weWillTestYou = ['I', 'Am', 'The', 'Fourth', 'Item'];
        $camelCasedRuleName = 'RuleName';

        $rule = $this->createMock(AbstractRule::class, [null]);
        $factory = $this->createMock(Factory::class);

        $rule->method('apply')
             ->willReturn(false);

        $factory->method('build')
                ->with($camelCasedRuleName, null)
                ->willReturn($rule);

        $validator = new class($factory) extends Validator {
            protected function init(): void
            {
                $this->addRule('item(Fifth):rule_name');
            }
        };
        
        $this->assertFalse($validator->validate($weWillTestYou));
    }
    
    public function test_modifier_item_string()
    {
        echo 'Validator - DSL: "item(i)" modifier with string - return char';

        $weWillTestYou = 'I am a string';
        $camelCasedRuleName = 'RuleName';

        $rule = $this->createMock(AbstractRule::class, ['m']);
        $factory = $this->createMock(Factory::class);

        $rule->method('apply')
             ->willReturn(true);

        $factory->method('build')
                ->with($camelCasedRuleName, 'm')
                ->willReturn($rule);

        $validator = new class($factory) extends Validator {
            protected function init(): void
            {
                $this->addRule('item(3):rule_name');
            }
        };

        $this->assertTrue($validator->validate($weWillTestYou));
    }
    
    public function test_modifier_item_numeric()
    {
        echo 'Validator - DSL: "item(i)" modifier with numeric - return digit';

        $weWillTestYou = 12345;
        $camelCasedRuleName = 'RuleName';

        $rule = $this->createMock(AbstractRule::class, [4]);
        $factory = $this->createMock(Factory::class);

        $rule->method('apply')
             ->willReturn(true);

        $factory->method('build')
                ->with($camelCasedRuleName, 4)
                ->willReturn($rule);

        $validator = new class($factory) extends Validator {
            protected function init(): void
            {
                $this->addRule('item(3):rule_name');
            }
        };

        $this->assertTrue($validator->validate($weWillTestYou));
    }
    
    public function test_modifier_item_not_string_or_numeric_or_array()
    {
        echo 'Validator - DSL: "item(i)" modifier with non-string, non-numeric and non-array - return null';

        $weWillTestYou = false;
        $camelCasedRuleName = 'RuleName';

        $rule = $this->createMock(AbstractRule::class, [null]);
        $factory = $this->createMock(Factory::class);

        $rule->method('apply')
             ->willReturn(false);

        $factory->method('build')
                ->with($camelCasedRuleName, null)
                ->willReturn($rule);

        $validator = new class($factory) extends Validator {
            protected function init(): void
            {
                $this->addRule('item(3):rule_name');
            }
        };
        
        $this->assertFalse($validator->validate($weWillTestYou));
    }
    
    public function test_modifier_each()
    {
        echo 'Validator - DSL: "each" modifier';

        $weWillTestYou = ['I', 'Have', 'Multiple', 'Items'];
        $camelCasedRuleName = 'RuleName';

        $factory = $this->createMock(Factory::class);

        $rule1 = $this->createMock(AbstractRule::class, ['I']);
        $rule1->method('apply')
              ->willReturn(true);

        $rule2 = $this->createMock(AbstractRule::class, ['Have']);
        $rule2->method('apply')
              ->willReturn(true);

        $rule3 = $this->createMock(AbstractRule::class, ['Multiple']);
        $rule3->method('apply')
              ->willReturn(true);

        $rule4 = $this->createMock(AbstractRule::class, ['Items']);
        $rule4->method('apply')
              ->willReturn(true);

        $factory->expects($this->at(0))
                ->method('build')
                ->with($camelCasedRuleName, 'I')
                ->willReturn($rule1);

        $factory->expects($this->at(1))
                ->method('build')
                ->with($camelCasedRuleName, 'Have')
                ->willReturn($rule2);

        $factory->expects($this->at(2))
                ->method('build')
                ->with($camelCasedRuleName, 'Multiple')
                ->willReturn($rule3);

        $factory->expects($this->at(3))
                ->method('build')
                ->with($camelCasedRuleName, 'Items')
                ->willReturn($rule4);

        $validator = new class($factory) extends Validator {
            protected function init(): void
            {
                $this->addRule('each:rule_name');
            }
        };

        $this->assertTrue($validator->validate($weWillTestYou));
    }
}
