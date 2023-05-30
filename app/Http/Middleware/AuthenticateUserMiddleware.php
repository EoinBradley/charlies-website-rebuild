<?php

namespace App\Http\Middleware;

use App\Models\Users\User;
use App\Repositories\PermissionsRepository;
use App\Repositories\UserRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AuthenticateUserMiddleware implements MiddlewareInterface
{
    public const AUTHENTICATED_USER = 'authenticated-user';

    public const USER_ROLES = 'user-roles';

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly PermissionsRepository $permissionsRepository
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!empty($_SESSION['userId'])) {
            $user = $this->userRepository->getUserById((int) $_SESSION['userId']);

            if ($user instanceof User) {
                return $handler->handle(
                    $request->withAttribute(self::AUTHENTICATED_USER, $user)
                        ->withAttribute(self::USER_ROLES, $this->permissionsRepository->getRolesForUser($user))
                );
            }

            unset($_SESSION);
        }

        return $handler->handle($request);
    }
}
