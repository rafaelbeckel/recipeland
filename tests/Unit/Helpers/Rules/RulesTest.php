<?php

namespace Tests\Unit\Helpers;

use Recipeland\Helpers\Rules\IsInstanceOf;
use Recipeland\Helpers\Rules\IsHttpMethod;
use Recipeland\Helpers\Rules\IsBetween;
use Recipeland\Helpers\Rules\IsPattern;
use Recipeland\Helpers\Rules\IsUrlPath;
use Recipeland\Helpers\Rules\IsNumeric;
use Recipeland\Helpers\Rules\NotEmpty;
use Recipeland\Helpers\Rules\Compare;
use Recipeland\Helpers\Rules\IsArray;
use Recipeland\Helpers\Rules\Equals;
use Recipeland\Helpers\Rules\IsType;
use Recipeland\Helpers\Rules\IsJwt;
use Recipeland\Helpers\Rules\IsUrl;
use Recipeland\Helpers\Rules\Min;
use Recipeland\Helpers\Rules\Max;
use BadMethodCallException;
use Tests\TestSuite;

class RulesTest extends TestSuite
{
    public function test_rule_compare()
    {
        echo 'Rule: compare';

        $equals = new Compare(['key1'=>'1', 'key2'=>2]);

        $this->assertTrue($equals->apply('key1', '<', 'key2'));
        $this->assertFalse($equals->apply('key1', '>', 'key2'));
        $this->assertTrue($equals->apply('key1', '<=', 'key2'));
        $this->assertFalse($equals->apply('key1', '>=', 'key2'));
        $this->assertFalse($equals->apply('key1', '==', 'key2'));
        $this->assertTrue($equals->apply('key1', '!=', 'key2'));
        $this->assertTrue($equals->apply('key1', '!==', 'key2'));
        $this->assertFalse($equals->apply('key1', '===', 'key2'));
        
        $this->expectException(BadMethodCallException::class);
        $equals->apply('key1', '==');
        
        $invalid = new Compare(['key1'=>'foo', 'key2'=>'foo']);
        $this->assertFalse($equals->apply('key1', '==', 'key2'));
    }
    
    public function test_rule_equals()
    {
        echo 'Rule: equals';

        $equals = new Equals('value');

        $this->assertTrue($equals->apply('value'));
        $this->assertFalse($equals->apply('im_not_the_same_who_am_i'));
    }

    public function test_rule_is_array()
    {
        echo 'Rule: is_array';

        $isarray = new IsArray(['foo', 'bar']);
        $notarray = new IsArray('im_not_an_array_who_am_i');

        $this->assertTrue($isarray->apply());
        $this->assertFalse($notarray->apply());
    }

    public function test_rule_is_between()
    {
        echo 'Rule: is_between';

        $isbetween = new IsBetween(5);

        $this->assertTrue($isbetween->apply(4, 6));
        $this->assertTrue($isbetween->apply(5, 6));
        $this->assertTrue($isbetween->apply(4, 5));
        $this->assertTrue($isbetween->apply(5, 5));
        $this->assertFalse($isbetween->apply(4, 4));
        $this->assertFalse($isbetween->apply(6, 6));
        
        $this->expectException(BadMethodCallException::class);
        $isbetween->apply(1);
    }
    
    public function test_rule_min()
    {
        echo 'Rule: min';

        $min = new Min(5);

        $this->assertTrue($min->apply(5));  // min. 5
        $this->assertTrue($min->apply(4));  // min. 4
        $this->assertFalse($min->apply(6)); // min. 6
        
        $this->expectException(BadMethodCallException::class);
        $min->apply(1, 2);
    }
    
    public function test_rule_max()
    {
        echo 'Rule: max';

        $max = new Max(5);

        $this->assertTrue($max->apply(5));  // max. 5
        $this->assertTrue($max->apply(6));  // max. 6
        $this->assertFalse($max->apply(4)); // max. 4
        
        $this->expectException(BadMethodCallException::class);
        $max->apply(1, 2);
    }

    public function test_rule_is_http_method()
    {
        echo 'Rule: is_http_method';

        $HTTP_Methods = ['HEAD', 'GET', 'POST', 'PUT',
                         'PATCH', 'DELETE', 'OPTIONS',
                         'PURGE', 'TRACE', 'CONNECT', ];

        foreach ($HTTP_Methods as $method) {
            $ishttpmethod = new IsHttpMethod($method);
            $this->assertTrue($ishttpmethod->apply());
        }

        $nothttpmethod = new IsHttpMethod('NOT_METHOD');
        $this->assertFalse($nothttpmethod->apply());
    }

    public function test_rule_is_pattern()
    {
        echo 'Rule: is_pattern';

        $ispattern = new IsPattern('123');
        $notpattern = new IsPattern('abc');

        $this->assertTrue($ispattern->apply('/[0-9]+/'));
        $this->assertFalse($notpattern->apply('/[0-9]+/'));
    }

    public function test_rule_is_type()
    {
        echo 'Rule: is_type';

        $integer = new IsType(123);
        $string = new IsType('123');
        $object = new IsType($this);

        $this->assertTrue($integer->apply('integer'));
        $this->assertTrue($string->apply('string'));
        $this->assertTrue($object->apply('object'));

        $this->assertFalse($integer->apply('string'));
        $this->assertFalse($string->apply('object'));
        $this->assertFalse($object->apply('integer'));
    }

    public function test_rule_is_instance_of()
    {
        echo 'Rule: is_instance_of';

        $instanceof = new IsInstanceOf($this);

        $this->assertTrue($instanceof->apply(RulesTest::class));
        $this->assertTrue($instanceof->apply(TestSuite::class));
        $this->assertFalse($instanceof->apply(IsInstanceOf::class));
    }
    
    public function test_rule_is_numeric()
    {
        echo 'Rule: is_numeric';

        $numeric = new IsNumeric('123');
        $notumeric = new IsNumeric('abc');

        $this->assertTrue($numeric->apply());
        $this->assertFalse($notumeric->apply());
    }

    public function test_rule_is_url_path()
    {
        echo 'Rule: is_url_path';

        $isurlpath = new IsUrlPath('/i/am/{a}/url/path?that=accepts&some=vars');
        $noturlpath = new IsUrlPath('/i/am/not a url /why? i cannot have spaces');

        $this->assertTrue($isurlpath->apply());
        $this->assertFalse($noturlpath->apply());
    }
    
    public function test_rule_is_jwt()
    {
        echo 'Rule: is_jwt';

        $isJwt = new IsJwt('Bearer iam.valid.jwt');
        $notJwt = new IsJwt('not. valid . jwt');

        $this->assertTrue($isJwt->apply());
        $this->assertFalse($notJwt->apply());
    }
    
    public function test_rule_is_url()
    {
        echo 'Rule: is_url';

        $isUrl = new IsUrl('http://example.com');
        $isUrl2 = new IsUrl('https://example.com/i/am/4/url/path?that=accepts&some=vars');
        $notUrl = new IsUrl('not. valid . url');

        $this->assertTrue($isUrl->apply());
        $this->assertTrue($isUrl2->apply());
        $this->assertFalse($notUrl->apply());
    }

    public function test_rule_not_empty()
    {
        echo 'Rule: not_empty';

        $notempty = new NotEmpty('Cogito, ergo sum');

        $empty_values = [0, '', [], '0', null, false];

        foreach ($empty_values as $value) {
            $empty = new NotEmpty($value);
            $this->assertFalse($empty->apply());
        }

        $this->assertTrue($notempty->apply());
    }
}
