<?php

namespace App\Http\Handlers;

use App\Http\Middleware\AuthenticateUserMiddleware;
use App\Models\Users\User;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class GetAuthUserHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $user = $request->getAttribute(AuthenticateUserMiddleware::AUTHENTICATED_USER);

        if ($user instanceof User === false) {
            return new EmptyResponse(401);
        }

        return new JsonResponse([
            'data' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
            ],
        ]);
    }
}
