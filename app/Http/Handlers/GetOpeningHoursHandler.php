<?php

namespace App\Http\Handlers;

use App\Models\OpeningTimes\OpeningTime;
use App\Repositories\OpeningHoursRepository;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class GetOpeningHoursHandler implements RequestHandlerInterface
{
    public function __construct(private readonly OpeningHoursRepository $openingHoursRepository)
    {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonResponse([
            'data' => $this
                ->openingHoursRepository
                ->getOpeningHoursForWeek()
                ->map(function (OpeningTime $openingTime) {
                    return [
                        'day' => $openingTime->date->dayName,
                        'openAt' => $openingTime->openedAt?->format('h:ia'),
                        'closeAt' => $openingTime->closedAt?->format('h:ia'),
                        'isException' => $openingTime->isException,
                        'exceptionDescription' => $openingTime->exceptionDescription,
                    ];
                }),
        ]);
    }
}