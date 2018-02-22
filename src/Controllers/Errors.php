<?php

namespace Recipeland\Controllers;

use Recipeland\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;

class Errors extends Controller
{
    
    public function not_found() {
        $this->response->setMethod(Response::HTTP_NOT_FOUND);
        $this->render();
    }
    
    public function method_not_allowed() {
        $this->response->setMethod(Response::HTTP_METHOD_NOT_ALLOWED);
        $this->render();
    }
    
    public function internal_server_error() {
        $this->response->setMethod(Response::HTTP_INTERNAL_SERVER_ERROR);
        $this->render();
    }

}