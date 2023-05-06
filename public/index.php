<?php

use Dotenv\Dotenv;
use Illuminate\Container\Container;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Relay\Relay;

require '../vendor/autoload.php';

try {
    Dotenv::createImmutable(__DIR__ . '/../')->load();
} catch (Throwable $exception) {
    (new Logger('app'))
        ->pushHandler(new StreamHandler('php://stdout', Level::Warning))
        ->error($exception->getMessage(), [
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ]);
}

/** @var Container $container */
$container = require __DIR__ . '/../bootstrap/container.php';

$queue = (require __DIR__ . '/../bootstrap/middleware.php')($container);

$request = ServerRequestFactory::fromGlobals();
$route = (require __DIR__ . '/../bootstrap/routes.php')($container, $request);

foreach ($route as $handler) {
    assert($handler instanceof RequestHandlerInterface || $handler instanceof MiddlewareInterface);
    $queue[] = $handler;
}

$replay = new Relay($queue);
(new SapiEmitter())->emit($replay->handle($request));
