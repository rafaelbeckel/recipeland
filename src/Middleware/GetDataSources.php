<?php

declare(strict_types=1);

namespace Recipeland\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Illuminate\Cache\Repository as Cache;
use Recipeland\Traits\ReturnsErrorResponse;
use Illuminate\Database\Capsule\Manager as DB;
use Recipeland\Interfaces\ScreamInterface as Logger;
use Psr\Http\Message\ServerRequestInterface as RequestInterface;
use Psr\Http\Server\RequestHandlerInterface as HandlerInterface;

class GetDataSources implements MiddlewareInterface
{
    use ReturnsErrorResponse;

    protected $db;
    protected $cache;

    public function __construct(DB $db, Cache $cache, Logger $logger)
    {
        $this->db = $db;
        $this->cache = $cache;
        $this->logger = $logger;
    }

    public function process(RequestInterface $request, HandlerInterface $next): ResponseInterface
    {
        try {
            $this->cache->getStore()->getRedis()->ping();
            $request = $request->withAttribute('cache', $this->cache);
        } catch (Exception $e) {
            $this->logger->critical($e->getMessage(), $e->getTrace());
            // Component unavailable, but App still works. Move on...
        }

        try {
            $this->db->getConnection()->getPdo();
            $request = $request->withAttribute('db', $this->db);
        } catch (Exception $e) {
            $this->logger->alert($e->getMessage(), $e->getTrace());

            return $this->errorResponse('service_unavailable', $request, $next);
        }

        return $next->handle($request);
    }
}
