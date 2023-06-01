<?php

namespace App\Http\Handlers;

use App\Http\Middleware\AuthenticateUserMiddleware;
use App\Models\Permissions\Role;
use App\Models\Permissions\Roles;
use App\Models\SiteConfigs\Config;
use App\Models\Users\User;
use App\Repositories\SiteConfigRepository;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class UpdateHomepageDescriptionHandler implements RequestHandlerInterface
{
    public function __construct(private readonly SiteConfigRepository $siteConfigRepository)
    {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /** @var User $user */
        $user = $request->getAttribute(AuthenticateUserMiddleware::AUTHENTICATED_USER);

        /** @var Roles $roles */
        $roles = $request->getAttribute(AuthenticateUserMiddleware::USER_ROLES);

        if ($roles->hasAccessTo(Role::UPDATE_HOMEPAGE_DESCRIPTION_ROLE) === false) {
            return new JsonResponse([
                'error' => 'Error 403: Forbidden'
            ], 403);
        }

        $description = $request->getParsedBody()['description'] ?? '';
        if (empty($description)) {
            return new JsonResponse([
                'errors' => [
                    'description' => ['Description is required.'],
                ],
            ], 422);
        }

        $this->siteConfigRepository->saveConfig(
            new Config(
                SiteConfigRepository::HOMEPAGE_DESCRIPTION,
                $description
            ),
            $user
        );

        return new EmptyResponse();
    }
}
