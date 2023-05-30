<?php

namespace App\ServiceProviders;

use Illuminate\Container\Container;
use PDO;

class DatabaseServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        $container->singleton(PDO::class, function () {
            return new PDO("mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']}", $_ENV['DB_USER'], $_ENV['DB_PASS']);
        });
    }
}
