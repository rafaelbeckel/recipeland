<?php

declare(strict_types=1);

use Illuminate\Database\Capsule\Manager as DB;

return function ($config) {
    $connection = $config->get('db.connection');
    $db = new DB();
    $db->addConnection($config->get('db.connections.'.$connection));
    $db->setAsGlobal();
    $db->bootEloquent();

    return $db;
};
