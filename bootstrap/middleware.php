<?php

use App\Http\Middleware\AuthenticateUserMiddleware;
use App\Http\Middleware\SessionMiddleware;
use Illuminate\Container\Container;
use Mezzio\Helper\BodyParams\BodyParamsMiddleware;

return function (Container $container): array {
    return [
        $container->make(BodyParamsMiddleware::class),
        $container->make(SessionMiddleware::class),
        $container->make(AuthenticateUserMiddleware::class),
    ];
};
