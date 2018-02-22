<?php

namespace Recipeland\Controllers;

use Recipeland\Controllers\Controller;

class Recipes extends Controller
{
    
    public function get() {
        $this->response->setContent(__METHOD__);
        $this->render();
    }
    
    public function create() {
        $this->response->setContent(__METHOD__);
        $this->render();
    }
    
    public function read() {
        $this->response->setContent(__METHOD__);
        $this->render();
    }
    
    public function update() {
        $this->response->setContent(__METHOD__);
        $this->render();
    }
    
    public function updateField() {
        $this->response->setContent(__METHOD__);
        $this->render();
    }
    
    public function remove() {
        $this->response->setContent(__METHOD__);
        $this->render();
    }
    
    public function rate() {
        $this->response->setContent(__METHOD__);
        $this->render();
    }

}