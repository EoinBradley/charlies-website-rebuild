<?php

namespace App\Http\Handlers\Events;

use App\Http\Middleware\AuthenticateUserMiddleware;
use App\Models\Artists\Artist;
use App\Models\Permissions\Role;
use App\Models\Permissions\Roles;
use App\Models\Users\User;
use App\Repositories\ArtistsRepository;
use Illuminate\Support\Arr;
use Laminas\Diactoros\Response\JsonResponse;
use PDO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Throwable;

class CreateArtistHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly ArtistsRepository $artistsRepository,
        private readonly LoggerInterface $logger,
        private readonly PDO $db
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /** @var User $user */
        $user = $request->getAttribute(AuthenticateUserMiddleware::AUTHENTICATED_USER);

        /** @var Roles $roles */
        $roles = $request->getAttribute(AuthenticateUserMiddleware::USER_ROLES);

        if ($roles->hasAccessTo(Role::MANAGE_ARTISTS_ROLE) === false) {
            return new JsonResponse([
                'error' => 'Error 403: Forbidden'
            ], 403);
        }

        try {
            $this->db->beginTransaction();

            $artist = $this->artistsRepository->saveArtist(
                $this->buildArtistFromRequest($request),
                $user
            );

            $this->db->commit();
        } catch (Throwable $exception) {
            $this->db->rollBack();
            $this->logger->error($exception->getMessage(), [
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ]);

            return new JsonResponse([
                'error' => 'Failed to create artist'
            ], 500);
        }

        return new JsonResponse([
            'data' => [
                'id' => $artist->id,
                'type' => 'artist',
                'attributes' => [
                    'name' => $artist->name,
                    'description' => $artist->description,
                ],
            ],
        ]);
    }

    public function buildArtistFromRequest(ServerRequestInterface $request): Artist
    {
        return new Artist(
            id: null,
            name: Arr::get($request->getParsedBody(), 'data.attributes.name', ''),
            description: Arr::get($request->getParsedBody(), 'data.attributes.description', ''),
        );
    }
}
