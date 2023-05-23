<?php

namespace App\ServiceProviders;

use Illuminate\Container\Container;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

class LoggerServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        $container->singleton(LoggerInterface::class, function () {
            return (new Logger('app'))
                ->pushHandler(new StreamHandler('php://stdout', Level::Warning))
                ->pushHandler(new StreamHandler(__DIR__ . '/../../../logs/error.log', Level::Error));
        });
    }
}