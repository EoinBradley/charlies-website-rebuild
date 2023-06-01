<?php

use App\Http\Handlers\AppHandler;
use App\Http\Handlers\GetAuthUserHandler;
use App\Http\Handlers\GetHomepageDescriptionHandler;
use App\Http\Handlers\GetOpeningHoursHandler;
use App\Http\Handlers\LoginHandler;
use App\Http\Handlers\LogoutHandler;
use App\Http\Handlers\UpdateHomepageDescriptionHandler;
use App\Http\Middleware\RequiresAuthenticationMiddleware;
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
        $r->addGroup('/api', function (RouteCollector $r) {
            $r->addRoute('GET', '/auth-user[/]', [
                RequiresAuthenticationMiddleware::class,
                GetAuthUserHandler::class,
            ]);

            $r->addRoute('POST', '/login[/]', [
                LoginHandler::class
            ]);

            $r->addRoute('POST', '/logout[/]', [
                LogoutHandler::class
            ]);

            $r->addRoute('GET', '/opening-hours[/]', [
                GetOpeningHoursHandler::class,
            ]);

            $r->addRoute('GET', '/homepage-description[/]', [
                RequiresAuthenticationMiddleware::class,
                GetHomepageDescriptionHandler::class,
            ]);

            $r->addRoute('PUT', '/homepage-description[/]', [
                RequiresAuthenticationMiddleware::class,
                UpdateHomepageDescriptionHandler::class,
            ]);
        });
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
