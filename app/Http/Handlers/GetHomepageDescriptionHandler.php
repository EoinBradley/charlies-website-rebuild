<?php

namespace App\Http\Handlers;

use App\Repositories\SiteConfigRepository;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class GetHomepageDescriptionHandler implements RequestHandlerInterface
{
    public function __construct(private readonly SiteConfigRepository $siteConfigRepository)
    {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonResponse([
            'data' => [
                'description' => $this->siteConfigRepository->getHomepageDescriptionConfig(),
            ]
        ]);
    }
}
