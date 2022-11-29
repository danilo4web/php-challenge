<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Entity\User;
use App\Domain\Repository\UserRepositoryInterface;
use App\Domain\Service\Contracts\UserServiceInterface;

class UserService implements UserServiceInterface
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
     * @return int
     * @throws \Exception
     */
    public function register(array $userData): int
    {
        $this->throwAnExceptionIfEmailAlreadyExists($userData['email']);

        $this->userEntity->setEmail($userData['email']);
        $this->userEntity->setPassword($userData['password']);
        $this->userEntity->setName($userData['name']);

        return $this->userRepository->insert($this->userEntity);
    }

    /**
     * @throws \Exception
     */
    private function throwAnExceptionIfEmailAlreadyExists(string $email): void
    {
        $userEmailExists = $this->userRepository->findByEmail($email);

        if ($userEmailExists) {
            throw new \Exception('Email already exists!');
        }
    }

    /**
     * @param int $userId
     * @return User
     */
    public function getUser(int $userId): User
    {
        return $this->userRepository->findById($userId);
    }
}
