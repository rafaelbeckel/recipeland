<?php

declare(strict_types=1);

namespace Recipeland;

use Dotenv\Dotenv;
use InvalidArgumentException;
use Illuminate\Config\Repository;
use Illuminate\Support\Facades\Facade;
use DI\Factory\RequestedEntry as Entry;
use Psr\Container\ContainerInterface as Container;
use Illuminate\Contracts\Config\Repository as RepositoryInterface;

class Config
{
    const DOTENV_DIR = __DIR__.'/..';

    protected $repository;

    public function __construct(string $directory)
    {
        $this->loadDotenv();

        $configArray = $this->buildConfigArray($directory);
        $this->repository = new Repository($configArray);
    }

    public function getRepository(): RepositoryInterface
    {
        return $this->repository;
    }

    public function get(string $key)
    {
        return $this->repository->get($key);
    }

    public function getInitializer(string $key = null): callable
    {
        if ($key) {
            return $this->get('initializers.'.$key);
        } else {
            return function (Container $c, Entry $entry) {
                $config = $c->get('config');
                $initializer = $config->get('initializers.'.$entry->getName());
                return $initializer($config);
            };
        }
    }

    public function runInitializer(string $key, ...$parameters)
    {
        $initializer = $this->getInitializer($key, true);
        $object = $initializer($this, ...$parameters);
        return $object;
    }

    public function setFacades($classes)
    {
        return Facade::setFacadeApplication($classes);
    }

    protected function loadDotenv(): void
    {
        $env = new Dotenv(self::DOTENV_DIR);
        $env->load();
    }

    protected function buildConfigArray(string $directory): array
    {
        $configArray = [];

        if (!is_dir($directory)) {
            throw new InvalidArgumentException('Config directory not found!');
        }

        $files = scandir($directory);

        foreach ($files as $file) {
            if ($file[0] == '.') {
                continue;
            }

            $fullpath = $directory.'/'.$file;
            $filename = pathinfo($fullpath)['filename']; //without extension

            if (is_dir($fullpath)) {
                $configArray[$filename] = $this->buildConfigArray($fullpath);
            } else {
                $configArray[$filename] = require $fullpath;
            }
        }

        return $configArray;
    }
}
