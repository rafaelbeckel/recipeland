<?php

namespace Tests;

use Mockery as m;
use PHPUnit\Framework\TestCase;

abstract class TestSuite extends TestCase
{
    public static function setUpBeforeClass()
    {
        $bold = "\033[2m";
        $normal = "\033[0m";
        echo "\n".$bold.static::class.$normal.": \n";
    }

    public function setUp()
    {
        echo ' ';
    }

    public function tearDown()
    {
        m::close();

        if ($this->getStatus() != 0) {
            $this->red($this->getStatusMessage());
        } else {
            $this->green('ok!');
        }

        echo "\n";
    }

    private function red($text)
    {
        $red = "\033[0;31m";
        $white = "\033[0m";
        echo ': '.$red.$text.$white;
    }

    private function green($text)
    {
        $green = "\033[0;32m";
        $white = "\033[0m";
        echo ': '.$green.$text.$white;
    }
}
