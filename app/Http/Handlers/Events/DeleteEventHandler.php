<?php

namespace App\Http\Handlers\Events;

use App\Http\Middleware\AuthenticateUserMiddleware;
use App\Models\Events\Exceptions\EventNotFound;
use App\Models\Permissions\Role;
use App\Models\Permissions\Roles;
use App\Models\Users\User;
use App\Repositories\EventsRepository;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\Response\JsonResponse;
use PDO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Throwable;

class DeleteEventHandler implements RequestHandlerInterface
{
    public function __construct(
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

        if ($roles->hasAccessTo(Role::MANAGE_ARTISTS_ROLE) === false) {
            return new JsonResponse([
                'error' => 'Error 403: Forbidden'
            ], 403);
        }

        try {
            $this->db->beginTransaction();

            $this->eventsRepository->deleteEvent(
                $this->eventsRepository->getEventById((int) $request->getAttribute('eventId')),
                $user
            );

            $this->db->commit();
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
                'error' => 'Failed to delete event'
            ], 500);
        }

        return new EmptyResponse();
    }
}
