<?php

declare(strict_types=1);

namespace App\Domain\Service\Contracts;

interface AuthServiceInterface
{
    public function authenticate(array $userData): string;
}
