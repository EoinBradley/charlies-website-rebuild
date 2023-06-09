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
                ->getOpeningHours()
                ->map(function (OpeningTime $openingTime) {
                    return [
                        'data' => [
                            'id' => $openingTime->date->dayName,
                            'type' => 'opening-hours',
                            'attributes' => [
                                'day' => $openingTime->date->dayName,
                                'open_at' => $openingTime->openedAt?->format('H:i'),
                                'close_at' => $openingTime->closedAt?->format('H:i'),
                            ],
                        ],
                    ];
                }),
        ]);
    }
}
