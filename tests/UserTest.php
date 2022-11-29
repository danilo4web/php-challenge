<?php

declare(strict_types=1);

namespace Tests;

use App\Domain\Entity\User;
use Illuminate\Http\Response;

/**
 * Class UserTest
 * @package Tests
 */
class UserTest extends BaseTestCase
{
    /**
     * @var \Slim\App
     */
    protected $app;

    /**
     * @throws \Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->app = $this->getAppInstance();
    }

    public function testRegisterUser(): void
    {
        $payload = [
            'name' => 'Danilo Pereira',
            'email' => rand(0, 1000) . '@email.com',
            'password' => 'secret123!'
        ];

        $request = $this->createRequest('POST', '/users');
        $request->withHeader('Content-Type', 'application/json');
        $request->getBody()->write((string)json_encode($payload));

        $response = $this->app->handle($request);
        $code = $response->getStatusCode();

        // Assert
        $this->assertEquals(201, $code);
    }

    public function testUserShouldLogin(): void
    {
        $user = [
            'name' => 'Test Name',
            'email' => time() . '@test.com',
            'password' => 'secret123!'
        ];

        $this->createUser($user['name'], $user['email'], $user['password']);

        $payload = [
            'email' => $user['email'],
            'password' => $user['password']
        ];

        $request = $this->createRequest('POST', '/login');
        $request->withHeader('Content-Type', 'application/json');
        $request->getBody()->write((string)json_encode($payload));

        $response = $this->app->handle($request);
        $code = $response->getStatusCode();

        $this->assertEquals(200, $code);
    }
}
