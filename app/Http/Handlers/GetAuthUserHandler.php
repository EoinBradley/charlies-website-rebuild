<?php

namespace App\Http\Handlers;

use App\Http\Middleware\AuthenticateUserMiddleware;
use App\Models\Permissions\Roles;
use App\Models\Users\User;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class GetAuthUserHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /** @var User $user */
        $user = $request->getAttribute(AuthenticateUserMiddleware::AUTHENTICATED_USER);

        /** @var Roles $roles */
        $roles = $request->getAttribute(AuthenticateUserMiddleware::USER_ROLES);

        return new JsonResponse([
            'data' => [
                'id' => $user->id,
                'type' => 'user',
                'attributes' => [
                    'username' => $user->username,
                    'email' => $user->email,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'roles' => $roles->pluck('name'),
                ],
            ],
        ]);
    }
}
