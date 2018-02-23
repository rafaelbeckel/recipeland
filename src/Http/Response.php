<?php

namespace Recipeland\Http;

use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class Response extends SymfonyResponse
{
    
    public function __construct($content = '', int $status = 200, array $headers = array())
    {
        parent::__construct($content, $status, $headers);
    }
    
}