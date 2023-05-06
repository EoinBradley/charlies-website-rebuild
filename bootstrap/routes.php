<?php

use App\Http\Handlers\AppHandler;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Illuminate\Container\Container;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use function FastRoute\simpleDispatcher;

/** @return RequestHandlerInterface[]|MiddlewareInterface[] */
return function (Container $container, ServerRequestInterface &$request): array {
    $dispatcher = simpleDispatcher(function (RouteCollector $r) {
        // $r->addRoute('GET', '[/]', [
        //     AppHandler::class
        // ]);
    });

    $routeInfo = $dispatcher->dispatch($request->getMethod(), $request->getUri()->getPath());

    foreach (Arr::get($routeInfo, 2, []) AS $key => $value) {
        $request = $request->withAttribute($key, $value);
    }

    if (Arr::get($routeInfo, 0) == Dispatcher::FOUND) {
        $handlers = collect(Arr::get($routeInfo, 1, [AppHandler::class]));
    } else {
        $handlers = collect([AppHandler::class]);
    }

    return $handlers->map(function ($handler) use ($container) {
        return $container->make($handler);
    })->toArray();
};