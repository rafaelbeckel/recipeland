<?php

namespace Recipeland\Http\Response;

use Psr\Http\Message\ResponseInterface;

class Sender
{
    protected $response;
    
    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }
    
    public function send(): Sender
    {
        $this->sendHeaders();
        $this->sendContent();
        $this->clearBuffers();
        return $this;
    }
    
    protected function sendHeaders(): void  
    {
        $r = $this->response;
        $h = $r->getHeaders();
        
        $version = $r->getProtocolVersion();
        $status  = $r->getStatusCode();
        $reason  = $r->getReasonPhrase();;
        $httpString = sprintf('HTTP/%s %s %s', $version, $status, $reason);
        
        // headers
        if (! headers_sent())
            foreach ($h as $key => $values)
                foreach ($values as $value)
                    header($key.': '.$value, false);
        
        // status
        header($httpString, true, $status);
    }
    
    protected function sendContent() 
    {
        echo $this->response->getBody();
    }
    
    protected function clearBuffers()
    {
        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
            
        } elseif (PHP_SAPI !== 'cli') {
            $this->closeOutputBuffers();
        }
    }
    
    private function closeOutputBuffers()
    {
        if (ob_get_level()) 
            ob_end_flush();
    }
}