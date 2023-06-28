<?php

namespace App\Http\Handlers\Events;

use App\Exceptions\FailedValidation;
use App\Http\Middleware\AuthenticateUserMiddleware;
use App\Models\Artists\Exceptions\ArtistNotFound;
use App\Models\Events\Event;
use App\Models\Events\Exceptions\EventNotFound;
use App\Models\Permissions\Role;
use App\Models\Permissions\Roles;
use App\Models\Users\User;
use App\Repositories\ArtistsRepository;
use App\Repositories\EventsRepository;
use Carbon\Carbon;
use Carbon\Exceptions\Exception as CarbonException;
use Illuminate\Support\Arr;
use Illuminate\Support\MessageBag;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\Response\JsonResponse;
use PDO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Throwable;

class UpdateEventHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly EventsRepository $eventsRepository,
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

            $currentEvent = $this->eventsRepository->getEventById((int) $request->getAttribute('eventId'));
            $newEvent = $this->buildEventFromRequest($request);

            if ($newEvent->isNotEqual($currentEvent)) {
                $this->eventsRepository->saveEvent($newEvent, $user);
            }

            $this->db->commit();
        } catch (FailedValidation $exception) {
            $this->db->rollBack();

            return new JsonResponse([
                'errors' => $exception->errors->getMessages(),
            ], 400);
        } catch (EventNotFound) {
            $this->db->rollBack();

            return new EmptyResponse(404);
        } catch (Throwable $exception) {
            $this->db->rollBack();
            $this->logger->error($exception->getMessage(), [
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ]);

            return new JsonResponse([
                'error' => 'Failed to update event'
            ], 500);
        }

        return new EmptyResponse();
    }

    /** @throws FailedValidation */
    public function buildEventFromRequest(ServerRequestInterface $request): Event
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
            id: (int) $request->getAttribute('eventId'),
            artist: $artist,
            startAt: $startAt
        );
    }
}
