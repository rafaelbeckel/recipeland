<?php

namespace Tests\Unit;

use InvalidArgumentException;
use Recipeland\Config;
use Tests\TestSuite;

class ConfigTest extends TestSuite
{
    public function test_config()
    {
        echo 'Config: build config repository from dir';
        
        $config = new Config(__DIR__.'/_fakeconfig');
        
        $this->assertEquals('bar', $config->get('config.foo'));
        $this->assertEquals('bim', $config->get('config.bar.baz'));
        $this->assertEquals('baz', $config->get('subdir.subconfig.bar'));
    }
    
    public function test_config_invalid_dir()
    {
        echo 'Config: raise error if dir not found';
        
        $this->expectException(InvalidArgumentException::class);
        $config = new Config(__DIR__.'/some/non/existent/dir');
    }
}
