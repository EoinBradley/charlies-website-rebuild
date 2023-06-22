<?php

namespace App\Http\Handlers\Events;

use App\Exceptions\FailedValidation;
use App\Http\Middleware\AuthenticateUserMiddleware;
use App\Models\Artists\Exceptions\ArtistNotFound;
use App\Models\Events\Event;
use App\Models\Permissions\Role;
use App\Models\Permissions\Roles;
use App\Models\Users\User;
use App\Repositories\ArtistsRepository;
use App\Repositories\EventsRepository;
use Carbon\Carbon;
use Carbon\Exceptions\Exception as CarbonException;
use Illuminate\Support\Arr;
use Illuminate\Support\MessageBag;
use Laminas\Diactoros\Response\JsonResponse;
use PDO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Throwable;

class CreateUpcomingEventsHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly ArtistsRepository $artistsRepository,
        private readonly EventsRepository $eventsRepository,
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

        if ($roles->hasAccessTo(Role::MANAGE_EVENTS_ROLE) === false) {
            return new JsonResponse([
                'error' => 'Error 403: Forbidden'
            ], 403);
        }

        try {
            $this->db->beginTransaction();

            $event = $this->eventsRepository->saveEvent(
                $this->buildEventFromRequest($request),
                $user
            );

            $this->db->commit();
        } catch (FailedValidation $exception) {
            return new JsonResponse([
                'errors' => $exception->errors->getMessages(),
            ], 400);
        } catch (Throwable $exception) {
            $this->db->rollBack();
            $this->logger->error($exception->getMessage(), [
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ]);

            return new JsonResponse([
                'error' => 'Failed to create event'
            ], 500);
        }

        return new JsonResponse([
            'data' => [
                'id' => $event->id,
                'type' => 'event',
                'attributes' => [
                    'start_at' => $event->startAt,
                    'artist' => [
                        'data' => [
                            'id' => $event->artist->id,
                            'type' => 'artist',
                            'attributes' => [
                                'name' => $event->artist->name,
                                'description' => $event->artist->description,
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }

    /** @throws FailedValidation */
    private function buildEventFromRequest(ServerRequestInterface $request): Event
    {
        $errors = new MessageBag();

        try {
            $startAt = Carbon::parse(Arr::get($request->getParsedBody(), 'data.attributes.start_at'));
            if ($startAt->isBefore(Carbon::now())) {
                $errors->add('start_at', "Your event date can't be in the past.");
            }
        } catch (CarbonException) {
            $errors->add('start_at', 'Invalid start date.');
        }

        try {
            $artist = $this->artistsRepository->getArtistById(
                (int) Arr::get($request->getParsedBody(), 'data.attributes.artist.data.id')
            );
        } catch (ArtistNotFound) {
            $errors->add('artist', 'Artist not found.');
        }

        if ($errors->isNotEmpty()) {
            throw FailedValidation::withErrors($errors);
        }

        return new Event(
            id: null,
            artist: $artist,
            startAt: $startAt
        );
    }
}
