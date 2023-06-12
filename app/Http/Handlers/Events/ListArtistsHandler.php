<?php

namespace App\Http\Handlers\Events;

use App\Models\Artists\Artist;
use App\Repositories\ArtistsRepository;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ListArtistsHandler implements RequestHandlerInterface
{
    public function __construct(private readonly ArtistsRepository $artistsRepository)
    {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonResponse([
            'data' => $this
                ->artistsRepository
                ->getArtists()
                ->map(function (Artist $artist) {
                    return [
                        'data' => [
                            'id' => $artist->id,
                            'type' => 'artist',
                            'attributes' => [
                                'name' => $artist->name,
                                'description' => $artist->description,
                            ],
                        ],
                    ];
                })
        ]);
    }
}
