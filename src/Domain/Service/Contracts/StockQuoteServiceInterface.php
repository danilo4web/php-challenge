<?php

declare(strict_types=1);

namespace App\Domain\Service\Contracts;

interface StockQuoteServiceInterface
{
    public function get(string $symbol): array;

    public function saveStockSearch(array $stockInfo, int $userId): int;

    public function getStockSearchLogByUserId(int $userId): array;
}
