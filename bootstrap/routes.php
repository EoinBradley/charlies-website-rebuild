<?php

use App\Http\Handlers\AppHandler;
use App\Http\Handlers\Events\CreateArtistHandler;
use App\Http\Handlers\Events\CreateUpcomingEventsHandler;
use App\Http\Handlers\Events\DeleteEventHandler;
use App\Http\Handlers\Events\ListArtistsHandler;
use App\Http\Handlers\Events\ListUpcomingEventsHandler;
use App\Http\Handlers\Events\UpdateArtistHandler;
use App\Http\Handlers\Events\UpdateEventHandler;
use App\Http\Handlers\GetAuthUserHandler;
use App\Http\Handlers\GetHomepageDescriptionHandler;
use App\Http\Handlers\GetOpeningHoursForWeekHandler;
use App\Http\Handlers\GetOpeningHoursHandler;
use App\Http\Handlers\LoginHandler;
use App\Http\Handlers\LogoutHandler;
use App\Http\Handlers\UpdateHomepageDescriptionHandler;
use App\Http\Handlers\UpdateOpeningHoursHandler;
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

            $r->addRoute('GET', '/opening-hours-for-week[/]', [
                GetOpeningHoursForWeekHandler::class,
            ]);

            $r->addRoute('GET', '/homepage-description[/]', [
                RequiresAuthenticationMiddleware::class,
                GetHomepageDescriptionHandler::class,
            ]);

            $r->addRoute('PUT', '/homepage-description[/]', [
                RequiresAuthenticationMiddleware::class,
                UpdateHomepageDescriptionHandler::class,
            ]);

            $r->addRoute('GET', '/opening-hours[/]', [
                RequiresAuthenticationMiddleware::class,
                GetOpeningHoursHandler::class
            ]);

            $r->addRoute('PUT', '/opening-hours[/]', [
                RequiresAuthenticationMiddleware::class,
                UpdateOpeningHoursHandler::class
            ]);

            $r->addRoute('GET', '/artists[/]', [
                RequiresAuthenticationMiddleware::class,
                ListArtistsHandler::class
            ]);

            $r->addRoute('POST', '/artists[/]', [
                RequiresAuthenticationMiddleware::class,
                CreateArtistHandler::class
            ]);

            $r->addRoute('PUT', '/artists/{artistId:\d+}[/]', [
                RequiresAuthenticationMiddleware::class,
                UpdateArtistHandler::class
            ]);

            $r->addRoute('GET', '/upcoming-events[/]', [
                ListUpcomingEventsHandler::class
            ]);

            $r->addRoute('POST', '/events[/]', [
                RequiresAuthenticationMiddleware::class,
                CreateUpcomingEventsHandler::class
            ]);

            $r->addRoute('PUT', '/events/{eventId:\d+}[/]', [
                RequiresAuthenticationMiddleware::class,
                UpdateEventHandler::class
            ]);

            $r->addRoute('DELETE', '/events/{eventId:\d+}[/]', [
                RequiresAuthenticationMiddleware::class,
                DeleteEventHandler::class
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
