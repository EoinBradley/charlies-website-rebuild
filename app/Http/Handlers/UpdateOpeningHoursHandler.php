<?php

namespace App\Http\Handlers;

use App\Http\Middleware\AuthenticateUserMiddleware;
use App\Models\OpeningTimes\OpeningTime;
use App\Models\Permissions\Role;
use App\Models\Permissions\Roles;
use App\Models\Users\User;
use App\Repositories\OpeningHoursRepository;
use Carbon\Carbon;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\Response\JsonResponse;
use PDO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Throwable;

class UpdateOpeningHoursHandler implements RequestHandlerInterface
{
    public function __construct(
        private readonly OpeningHoursRepository $openingHoursRepository,
        private readonly PDO $db,
        private readonly LoggerInterface $logger
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /** @var User $user */
        $user = $request->getAttribute(AuthenticateUserMiddleware::AUTHENTICATED_USER);

        /** @var Roles $roles */
        $roles = $request->getAttribute(AuthenticateUserMiddleware::USER_ROLES);

        if ($roles->hasAccessTo(Role::UPDATE_OPENING_HOURS_ROLE) === false) {
            return new JsonResponse([
                'error' => 'Error 403: Forbidden'
            ], 403);
        }

        $days = collect($request->getParsedBody()['days'] ?? [])
            ->map(function (array $day) {
                return new OpeningTime(
                    !is_null($day['open_at'])
                        ? Carbon::createFromFormat('H:i', $day['open_at'])
                        : null,
                    !is_null($day['close_at'])
                        ? Carbon::createFromFormat('H:i', $day['close_at'])
                        : null,
                    Carbon::now()->next($day['day'])
                );
            });

        try {
            $this->db->beginTransaction();

            $this->openingHoursRepository
                ->getOpeningHours()
                ->each(function (OpeningTime $openingTime) use ($days, $user) {
                    /** @var OpeningTime $day */
                    $day = $days->first(function (OpeningTime $day) use ($openingTime) {
                        return $day->date->dayOfWeek === $openingTime->date->dayOfWeek;
                    });

                    if (is_null($day) || $openingTime->isNotEqual($day)) {
                        $this->openingHoursRepository->saveOpeningHours($day, $user);
                    }
                });

            $this->db->commit();
        } catch (Throwable $exception) {
            $this->db->rollBack();
            $this->logger->error($exception->getMessage(), [
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ]);

            return new JsonResponse([
                'error' => 'Failed to update opening hours'
            ], 500);
        }

        return new EmptyResponse();
    }
}
