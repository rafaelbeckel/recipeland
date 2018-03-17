<?php

use Illuminate\Cache\Repository as Cache;
use Illuminate\Cache\RedisStore as Driver;
use Illuminate\Redis\RedisManager as Redis;

return function ($config) {
    return new Cache(
        new Driver(
            new Redis('predis', $config->get('db.redis'))
        )
    );
};
