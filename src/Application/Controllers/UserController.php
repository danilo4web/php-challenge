<?php

declare(strict_types=1);

namespace App\Application\Controllers;

use App\Domain\Service\Contracts\UserServiceInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UserController
{
    /**
     * @param UserServiceInterface $userService
     */
    public function __construct(private UserServiceInterface $userService)
    {
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function register(Request $request, Response $response): Response
    {
        $data = json_decode((string) $request->getBody(), true);

        try {
            $validateErrors = $this->validatePayload($data);

            if (count($validateErrors)) {
                throw new \Exception(implode(',', $validateErrors));
            }

            $userId = $this->userService->register($data);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['message' => $e->getMessage()]));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(422);
        }

        $response->getBody()->write(json_encode(['userId' => $userId]));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(201);
    }

    private function validatePayload(array $userData): array
    {
        $errors = [];

        if (empty($userData['name'])) {
            $errors[] = 'Name is required!';
        }

        if (empty($userData['email'])) {
            $errors[] = 'Email is required!';
        }

        if (empty($userData['password'])) {
            $errors[] = 'Password is required!';
        }

        return $errors;
    }
}
