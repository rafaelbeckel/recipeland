<?php


/**
 * Created by PhpStorm.
 * User: leo108
 * Date: 2017/8/14
 * Time: 15:44
 */
 
namespace Recipeland\Data\Cache\Bridge;

use Illuminate\Cache\Repository as Cache;
use Psr\SimpleCache\CacheInterface;

class Bridge implements CacheInterface
{
    public function get($key, $default = null)
    {
        return Cache::get($key, $default);
    }

    public function set($key, $value, $ttl = null)
    {
        Cache::put($key, $value, $this->ttlToMinutes($ttl));

        return true;
    }

    public function delete($key)
    {
        return Cache::forget($key);
    }

    public function clear()
    {
        return Cache::flush();
    }

    public function getMultiple($keys, $default = null)
    {
        return Cache::many($keys);
    }

    public function setMultiple($values, $ttl = null)
    {
        Cache::putMany($values, $this->ttlToMinutes($ttl));

        return true;
    }

    public function deleteMultiple($keys)
    {
        foreach ($keys as $key) {
            $this->delete($key);
        }
    }

    public function has($key)
    {
        return Cache::has($key);
    }

    protected function ttlToMinutes($ttl)
    {
        if (is_null($ttl)) {
            return null;
        }
        if ($ttl instanceof \DateInterval) {
            return $ttl->days * 86400 + $ttl->h * 3600 + $ttl->i * 60;
        }

        return $ttl / 60;
    }
}