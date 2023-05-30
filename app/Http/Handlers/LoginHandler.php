<?php

namespace App\Http\Handlers;

use App\Models\Users\Exceptions\InvalidLoginCredentials;
use App\Repositories\UserRepository;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class LoginHandler implements RequestHandlerInterface
{
    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $this->userRepository->authenticateUser(
                $request->getParsedBody()['email'] ?? '',
                $request->getParsedBody()['password'] ?? ''
            );
        } catch (InvalidLoginCredentials $exception) {
            return new JsonResponse([
                'errors' => [
                    'email' => ['Email & Password does not match with our record.'],
                ]
            ], 422);
        }

        return new EmptyResponse();
    }
}
