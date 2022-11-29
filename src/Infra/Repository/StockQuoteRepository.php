<?php

declare(strict_types=1);

namespace App\Infra\Repository;

use App\Domain\Entity\StockQuote;
use App\Domain\Repository\StockQuoteRepositoryInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;

class StockQuoteRepository implements StockQuoteRepositoryInterface
{
    /**
     * @param EntityManager $em
     */
    public function __construct(private EntityManager $em)
    {
    }

    /**
     * @param StockQuote $stockLog
     * @return int
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function insert(StockQuote $stockLog): int
    {
        $this->em->persist($stockLog);
        $this->em->flush();

        return $stockLog->getId();
    }

    /**
     * @param int $userId
     * @return array
     */
    public function findByUserId(int $userId): array
    {
        $stockLogRepository = $this->em->getRepository(StockQuote::class);

        return $stockLogRepository->createQueryBuilder('s')
            ->select('s.json_response')
            ->where('s.user = :user')
            ->setParameter('user', $userId)
            ->orderBy('s.id', 'DESC')
            ->getQuery()
            ->getArrayResult();
    }
}
