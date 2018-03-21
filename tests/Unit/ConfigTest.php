<?php

namespace Tests\Unit;

use InvalidArgumentException;
use Recipeland\Config;
use Tests\TestSuite;

class ConfigTest extends TestSuite
{
    public function test_config()
    {
        $config = new Config(__DIR__.'/_fakeconfig');
        
        $this->assertEquals('bar', $config->get('config.foo'));
        $this->assertEquals('bim', $config->get('config.bar.baz'));
        $this->assertEquals('baz', $config->get('subdir.subconfig.bar'));
    }
    
    public function test_config_invalid_dir()
    {
        $this->expectException(InvalidArgumentException::class);
        $config = new Config(__DIR__.'/some/non/existent/dir');
    }
}
