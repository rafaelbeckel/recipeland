<?php

namespace Recipeland\Controllers;

use Recipeland\Controllers\Controller;

class Recipes extends Controller
{
    public function get()
    {
        $this->send('Hi, '.__METHOD__.'!');
    }
    
    public function create()
    {
        $this->send('Hi, '.__METHOD__.'!');
    }
    
    public function read()
    {
        $this->send('Hi, '.__METHOD__.'!');
    }
    
    public function update()
    {
        $this->send('Hi, '.__METHOD__.'!');
    }
    
    public function updateField()
    {
        $this->send('Hi, '.__METHOD__.'!');
    }
    
    public function remove()
    {
        $this->send('Hi, '.__METHOD__.'!');
    }
    
    public function rate()
    {
        $this->send('Hi, '.__METHOD__.'!');
    }
}
