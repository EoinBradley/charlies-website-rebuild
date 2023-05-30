<?php

namespace App\Http\Middleware;

use App\SessionHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class SessionMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly SessionHandler $sessionHandler
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        session_set_save_handler($this->sessionHandler, true);
        session_start();
        return $handler->handle($request);
    }
}
