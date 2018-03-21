<?php declare(strict_types=1);

namespace Recipeland\Http;

use GuzzleHttp\Psr7\ServerRequest;
use Recipeland\Interfaces\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;

class Request extends ServerRequest implements RequestInterface
{
    protected $data;
    
    public function getParam(string $key, $default = '')
    {
        return $this->getParamFrom('body', $key, $default);
    }
    
    public function getQueryParam(string $key, $default = '')
    {
        return $this->getParamFrom('query', $key, $default);
    }
    
    public function getParamFrom(string $where, string $key, $default = '')
    {
        return $this->data[$where][$key] ?? $default;
    }
    
    public static function upgrade(ServerRequestInterface $request): RequestInterface
    {
        $upgraded = (new static(
            $request->getMethod(),
            (string) $request->getUri(),
            $request->getHeaders(),
            (string) $request->getBody(),
            $request->getProtocolVersion()
        ))->withCookieParams($request->getCookieParams())
          ->withQueryParams($request->getQueryParams())
          ->withParsedBody($request->getParsedBody())
          ->withUploadedFiles($request->getUploadedFiles());
          
        foreach ($request->getAttributes() as $key => $attribute) {
            $upgraded = $upgraded->withAttribute($key, $attribute);
        }
        
        $upgraded->populateData($request);
        
        return $upgraded;
    }
    
    protected function populateData(ServerRequestInterface $request): void
    {
        $this->data['uri'] = $request->getUri();
        $this->data['body'] = json_decode((string) $request->getBody(), true);
        $this->data['files'] = $request->getUploadedFiles();
        $this->data['query'] = $request->getQueryParams();
        $this->data['method'] = $request->getMethod();
        $this->data['headers'] = $request->getHeaders();
        $this->data['cookies'] = $request->getCookieParams();
        $this->data['version'] = $request->getProtocolVersion();
        $this->data['attributes'] = $request->getAttributes();
    }
}
