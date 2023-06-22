<?php

namespace App\Http\Handlers\Events;

use App\Models\Events\Event;
use App\Repositories\EventsRepository;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ListUpcomingEventsHandler implements RequestHandlerInterface
{
    public function __construct(private readonly EventsRepository $eventsRepository)
    {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonResponse([
            'data' => $this
                ->eventsRepository
                ->getUpcomingEvents()
                ->map(function (Event $event) {
                    return [
                        'data' => [
                            'id' => $event->id,
                            'type' => 'event',
                            'attributes' => [
                                'start_at' => $event->startAt->format('Y-m-d H:i:s'),
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
                    ];
                })
        ]);
    }
}
