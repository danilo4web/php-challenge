<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\StockQuote;

interface StockQuoteRepositoryInterface
{
    public function insert(StockQuote $stockLog): int;

    public function findByUserId(int $userId): array;
}
