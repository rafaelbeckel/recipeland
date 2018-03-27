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
        // @codeCoverageIgnoreStart
        } catch (\Predis\Connection\ConnectionException $e) {
            // Component unavailable, but App still works. Move on...
            $this->logger->critical($e->getMessage(), $e->getTrace());
        }
        // @codeCoverageIgnoreEnd

        try {
            $this->db->getConnection()->getPdo();
            $request = $request->withAttribute('db', $this->db->getConnection());
        // @codeCoverageIgnoreStart
        } catch (\PDOException $e) {
            $this->logger->alert($e->getMessage(), $e->getTrace());

            return $this->errorResponse('service_unavailable', $request, $next);
        }
        // @codeCoverageIgnoreEnd

        return $next->handle($request);
    }
}
