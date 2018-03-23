<?php 

declare(strict_types=1);

namespace Recipeland\Controllers;

use Recipeland\Controllers\AbstractController as Controller;
use Psr\Http\Message\ServerRequestInterface as Request;

class Main extends Controller
{
    public function home(Request $request)
    {
        $this->setJsonResponse(['Recipeland' => 'OK!']);
    }
}
