<?php

namespace Tests\Unit\Helpers\Validators;

use Recipeland\Helpers\Validators\RoutesArrayValidator;
use Recipeland\Helpers\Rules\RuleFactory;
use Tests\TestSuite;

class RoutesArrayValidatorTest extends TestSuite
{
    protected $v;

    public function setUp()
    {
        parent::setUp();

        $factory = new RuleFactory();

        $this->v = new RoutesArrayValidator($factory);
    }

    public function test_Route_should_not_accept_non_array_arguments()
    {
        echo 'RoutesArrayValidator: should only accept arrays';

        $arguments = [
            'I am not an array!',
            function () {
            },
            new class() {
            },
            12345.678,
            12345,
            true,
            null,
        ];

        foreach ($arguments as $value) {
            $this->assertFalse($this->v->validate($value));
        }
    }

    public function test_Route_should_not_accept_empty_array()
    {
        echo 'RoutesArrayValidator: should not accept empty array';

        $routes = [];

        $this->assertFalse($this->v->validate($routes));
    }

    public function test_Route_array_validation_missing_string()
    {
        echo 'RoutesArrayValidator: routes array must be complete';

        $routes = [
            ['GET', '/foo', 'Recipes@get'],
            ['POST', '/foo', 'Recipes@create'],
            ['PUT', 'Recipes@update'],  //Missing string
        ];

        $this->assertFalse($this->v->validate($routes));
    }

    public function test_Route_array_validation_extra_string()
    {
        echo 'RoutesArrayValidator: routes array should not have extra string';

        $routes = [
            ['GET', '/foo', 'Recipes@get'],
            ['POST', '/foo', 'Recipes@create'],
            ['PUT', '/foo', 'Recipes@update', 'Too Many Strings'],
        ];

        $this->assertFalse($this->v->validate($routes));
    }

    public function test_Route_array_validation_first_element()
    {
        echo 'RoutesArrayValidator: first element must be HTTP method';

        $routes = [
            ['I am not an HTTP Verb', '/foo', 'Recipes@get'],
        ];

        $this->assertFalse($this->v->validate($routes));
    }

    public function test_Route_array_validation_second_element()
    {
        echo 'RoutesArrayValidator: second element must be URL path';

        $routes = [
            ['GET', '???', 'Recipes@get'],
        ];

        $this->assertFalse($this->v->validate($routes));
    }

    public function test_Route_array_validation_third_element()
    {
        echo 'RoutesArrayValidator: third element must be Controller@action';

        $routes = [
            ['GET', '/foo', 'No_Symbol'],
        ];

        $this->assertFalse($this->v->validate($routes));
    }

    public function test_Route_array_validation_valid_array()
    {
        echo 'RoutesArrayValidator: testing a valid entry';

        $routes = [
            ['GET', '/i', 'I_@_am'],
            ['GET', '/am', 'A_@_totally'],
            ['PUT', '/valid', 'Valid_@_entry'],
        ];

        $this->assertTrue($this->v->validate($routes));
    }
}
