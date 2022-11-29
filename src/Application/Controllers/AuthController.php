<?php

declare(strict_types=1);

namespace App\Application\Controllers;

use App\Domain\Service\Contracts\AuthServiceInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AuthController
{
    /**
     * @param AuthServiceInterface $authService
     */
    public function __construct(private AuthServiceInterface $authService)
    {
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function login(Request $request, Response $response): Response
    {
        $data = json_decode((string) $request->getBody(), true);

        try {
            $validToken = $this->authService->authenticate($data);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode($e->getMessage()));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(422);
        }

        $response->getBody()->write(json_encode(['token' => $validToken]));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}
