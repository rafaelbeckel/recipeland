<?php declare(strict_types=1);

namespace Recipeland\Controllers;

use Recipeland\Controllers\Controller;

class Recipes extends Controller
{
    public function get()
    {
        $this->setResponseBody('Hi, '.__METHOD__.'!');
    }
    
    public function create()
    {
        $this->setResponseBody('Hi, '.__METHOD__.'!');
    }
    
    public function read()
    {
        $this->setResponseBody('Hi, '.__METHOD__.'!');
    }
    
    public function update()
    {
        $this->setResponseBody('Hi, '.__METHOD__.'!');
    }
    
    public function updateField()
    {
        $this->setResponseBody('Hi, '.__METHOD__.'!');
    }
    
    public function remove()
    {
        $this->setResponseBody('Hi, '.__METHOD__.'!');
    }
    
    public function rate()
    {
        $this->setResponseBody('Hi, '.__METHOD__.'!');
    }
}
