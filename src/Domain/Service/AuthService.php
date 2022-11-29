<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Entity\User;
use App\Domain\Repository\UserRepositoryInterface;
use App\Domain\Service\Contracts\AuthServiceInterface;
use Firebase\JWT\JWT;

class AuthService implements AuthServiceInterface
{
    /**
     * @param UserRepositoryInterface $userRepository
     * @param User $userEntity
     */
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private User $userEntity
    ) {
    }

    /**
     * @param array $userData
     * @return string
     * @throws \Exception
     */
    public function authenticate(array $userData): string
    {
        $user = $this->userRepository->findByEmail($userData['email']);

        if (!$user instanceof User) {
            throw new \Exception('Credentials invalid! Email or password invalid!', 401);
        }

        if (!$this->passwordIsValid($user, $userData['password'])) {
            throw new \Exception('Credentials invalid! Email or password invalid!', 401);
        }

        return $this->generateToken($user);
    }

    /**
     * @param User $user
     * @param string $password
     * @return bool
     */
    private function passwordIsValid(User $user, string $password): bool
    {
        return password_verify($password, $user->getPassword());
    }

    /**
     * @param User $user
     * @return string
     */
    private function generateToken(User $user): string
    {
        $iat = time();
        $expiredAt = strtotime('+1 day', $iat);

        $tokenData = [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'iat' => $iat,
            'exp' => $expiredAt
        ];

        return JWT::encode($tokenData, $_ENV['JWT_SECRET'], 'HS256');
    }
}
