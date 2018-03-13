<?php

namespace Tests\Unit\Helpers;

use Recipeland\Helpers\Rules\AbstractRule;
use Recipeland\Helpers\Validator;
use Recipeland\Helpers\Factory;
use Tests\TestSuite;
use Mockery as m;

class ValidatorTest extends TestSuite
{
    protected $rule;
    protected $factory;

    public function setUp()
    {
        $this->factory = m::mock(Factory::class);
    }

    public function test_call_the_right_rule_true()
    {
        echo 'Validator - DSL: should call the right rule class - return true';

        $weWillTestYou = 'i_will_return_true';
        $camelCasedRuleName = 'RuleName';

        $rule = m::mock(AbstractRule::class, ['i_will_return_true']);
        $rule->shouldReceive('apply')->once()
             ->andReturn(true);

        $this->factory
             ->shouldReceive('build')->once()
             ->with($camelCasedRuleName, $weWillTestYou)
             ->andReturn($rule);

        $validator = new class($this->factory) extends Validator {
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

        $rule = m::mock(AbstractRule::class, ['i_will_return_false_im_so_falsy']);
        $rule->shouldReceive('apply')->once()
             ->andReturn(false)
             ->shouldReceive('getMessage')->once()
             ->andReturn('You are False!');

        $this->factory
             ->shouldReceive('build')->once()
             ->with($camelCasedRuleName, $weWillTestYou)
             ->andReturn($rule);

        $validator = new class($this->factory) extends Validator {
            protected function init(): void
            {
                $this->addRule('rule_name');
            }
        };

        $this->assertFalse($validator->validate($weWillTestYou));
        $this->assertEquals($validator->getMessage(), 'You are False!');
    }

    public function test_modifier_count()
    {
        echo 'Validator - DSL: "count" modifier';

        $weWillTestYou = ['I', 'Have', '4', 'Items'];
        $camelCasedRuleName = 'RuleName';

        $rule = m::mock(AbstractRule::class, [4]);
        $rule->shouldReceive('apply')
             ->once()
             ->andReturn(true);

        $this->factory
             ->shouldReceive('build')
             ->with($camelCasedRuleName, 4)->once()
             ->andReturn($rule);

        $validator = new class($this->factory) extends Validator {
            protected function init(): void
            {
                $this->addRule('count:rule_name');
            }
        };

        $this->assertTrue($validator->validate($weWillTestYou));
    }

    public function test_modifier_item()
    {
        echo 'Validator - DSL: "item(i)" modifier';

        $weWillTestYou = ['I', 'Am', 'The', 'Fourth', 'Item'];
        $camelCasedRuleName = 'RuleName';

        $rule = m::mock(AbstractRule::class, ['Fourth']);
        $rule->shouldReceive('apply')->once()
             ->andReturn(true);

        $this->factory->shouldReceive('build')
             ->with($camelCasedRuleName, 'Fourth')->once()
             ->andReturn($rule);

        $validator = new class($this->factory) extends Validator {
            protected function init(): void
            {
                $this->addRule('item(3):rule_name');
            }
        };

        $this->assertTrue($validator->validate($weWillTestYou));
    }

    public function test_modifier_each()
    {
        echo 'Validator - DSL: "each" modifier';

        $weWillTestYou = ['I', 'Have', 'Multiple', 'Items'];
        $camelCasedRuleName = 'RuleName';

        $rule1 = m::mock(AbstractRule::class, ['I']);
        $rule1->shouldReceive('apply')->once()
              ->andReturn(true);

        $rule2 = m::mock(AbstractRule::class, ['Have']);
        $rule2->shouldReceive('apply')->once()
              ->andReturn(true);

        $rule3 = m::mock(AbstractRule::class, ['Multiple']);
        $rule3->shouldReceive('apply')->once()
              ->andReturn(true);

        $rule4 = m::mock(AbstractRule::class, ['Items']);
        $rule4->shouldReceive('apply')->once()
              ->andReturn(true);

        $this->factory->shouldReceive('build')
             ->with($camelCasedRuleName, 'I')
             ->andReturn($rule1);

        $this->factory->shouldReceive('build')
             ->with($camelCasedRuleName, 'Have')
             ->andReturn($rule2);

        $this->factory->shouldReceive('build')
             ->with($camelCasedRuleName, 'Multiple')
             ->andReturn($rule3);

        $this->factory->shouldReceive('build')
             ->with($camelCasedRuleName, 'Items')
             ->andReturn($rule4);

        $validator = new class($this->factory) extends Validator {
            protected function init(): void
            {
                $this->addRule('each:rule_name');
            }
        };

        $this->assertTrue($validator->validate($weWillTestYou));
    }
}
