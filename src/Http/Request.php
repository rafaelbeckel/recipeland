<?php declare(strict_types=1);

namespace Recipeland\Http;

use GuzzleHttp\Psr7\ServerRequest;
use Recipeland\Interfaces\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;

class Request extends ServerRequest implements RequestInterface
{
    public function getParam(string $key, $default = '')
    {
        $params = $this->getQueryParams();
        return $params[$key] ?? $default;
    }
    
    public static function upgrade(ServerRequestInterface $request): RequestInterface
    {
        $upgraded = (new static(
            $request->getMethod(),
            $request->getUri(),
            $request->getHeaders(),
            $request->getBody(),
            $request->getProtocolVersion()
        ))->withCookieParams($request->getCookieParams())
          ->withQueryParams($request->getQueryParams())
          ->withParsedBody($request->getParsedBody())
          ->withUploadedFiles($request->getUploadedFiles());
         
        $upgraded->attributes = $request->getAttributes();
        
        return $upgraded;
    }
}
