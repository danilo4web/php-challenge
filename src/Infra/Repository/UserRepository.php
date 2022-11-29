<?php

declare(strict_types=1);

namespace App\Infra\Repository;

use App\Domain\Entity\User;
use App\Domain\Log\Log;
use App\Domain\Repository\UserRepositoryInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;

class UserRepository implements UserRepositoryInterface
{
    public function __construct(private EntityManager $em)
    {
    }

    /**
     * @param User $user
     * @return int
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function insert(User $user): int
    {
        $this->em->persist($user);
        $this->em->flush();

        return $user->getId();
    }

    /**
     * @param int $id
     * @return User|null
     */
    public function findById(int $id): ?User
    {
        return $this->em->getRepository(User::class)->find($id);
    }

    /**
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User
    {
        return $this->em->getRepository(User::class)->findOneBy(['email' => $email]);
    }
}
