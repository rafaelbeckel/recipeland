<?php

declare(strict_types=1);

namespace Recipeland\Http\Response;

use Psr\Http\Message\ResponseInterface;
use Recipeland\Interfaces\SenderInterface;

class Sender implements SenderInterface
{
    protected $response;

    public function send(ResponseInterface $response): void
    {
        $this->response = $response;

        $this->sendHeaders();
        $this->sendContent();
        $this->clearBuffers();
    }

    protected function sendHeaders(): void
    {
        $response = $this->response;
        $headers = $response->getHeaders();
        $version = $response->getProtocolVersion();
        $status = $response->getStatusCode();
        $reason = $response->getReasonPhrase();

        $httpString = sprintf('HTTP/%s %s %s', $version, $status, $reason);

        // custom headers
        foreach ($headers as $key => $values) {
            foreach ($values as $value) {
                header($key.': '.$value, false);
            }
        }

        // status
        header($httpString, true, $status);
    }

    protected function sendContent()
    {
        echo (string) $this->response->getBody();
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
        if (ob_get_level()) {
            ob_end_flush();
        }
    }
}
