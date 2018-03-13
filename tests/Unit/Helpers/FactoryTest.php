<?php

namespace Tests\Unit\Helpers;

use Recipeland\Helpers\Factory;
use Tests\TestSuite;

class FactoryTest extends TestSuite
{
    public function test_build_with_local_namespace()
    {
        echo 'Factory - build instance of a given class name from local namespace';

        $factory = new class() extends Factory {
            protected $namespace = __NAMESPACE__;
        };

        $buildme = $factory->build('BuildMe');

        $this->assertEquals($buildme->sayHello(), 'Hello from local namespace!');
    }

    public function test_build_with_external_namespace()
    {
        echo 'Factory - build instance of a given class name';

        $factory = new class(__NAMESPACE__.'\FarAwayLand') extends Factory {
        };

        $buildme = $factory->build('ExternalBuildMe');

        $this->assertEquals($buildme->sayHello(), 'Hello From Far Away Land!');
    }
}
