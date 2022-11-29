<?php

declare(strict_types=1);

namespace App\Domain\Service\Contracts;

use App\Domain\Entity\User;

interface UserServiceInterface
{
    public function register(array $userData): int;

    public function getUser(int $userId): User;
}
