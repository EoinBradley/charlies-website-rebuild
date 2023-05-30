<?php

use App\Http\Middleware\AuthenticateUserMiddleware;
use App\Http\Middleware\SessionMiddleware;
use Illuminate\Container\Container;

return function (Container $container): array {
    return [
        $container->make(SessionMiddleware::class),
        $container->make(AuthenticateUserMiddleware::class),
    ];
};
