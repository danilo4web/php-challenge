<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Entity\StockQuote;
use App\Domain\Repository\StockQuoteRepositoryInterface;
use App\Domain\Service\Contracts\StockQuoteServiceInterface;
use App\Infra\Gateway\StooqApiGateway;
use App\Infra\Service\Mail\MailService;
use App\Infra\Service\Queue\Rabbitmq\QueueProducerService;

class StockQuoteService implements StockQuoteServiceInterface
{
    /**
     * @param StooqApiGateway $stooqApi
     * @param StockQuoteRepositoryInterface $stockLogRepository
     * @param StockQuote $stockLogEntity
     * @param UserService $userService
     * @param MailService $mailService
     */
    public function __construct(
        private StooqApiGateway $stooqApi,
        private StockQuoteRepositoryInterface $stockLogRepository,
        private StockQuote $stockLogEntity,
        private UserService $userService,
        private QueueProducerService $queueProducerService
    ) {
    }

    /**
     * @param string $symbol
     * @return array
     */
    public function get(string $symbol): array
    {
        return $this->stooqApi->find($symbol);
    }

    /**
     * @param array $stockInfo
     * @param int $userId
     * @return int
     */
    public function saveStockSearch(array $stockInfo, int $userId): int
    {
        $userEntity = $this->userService->getUser($userId);
        $this->stockLogEntity->setUser($userEntity);
        $this->stockLogEntity->setJsonResponse((string) json_encode($stockInfo));

        $id = $this->stockLogRepository->insert(stockLog: $this->stockLogEntity);

        if ($id) {
            $this->queueProducerService->produce(email: $userEntity->getEmail(), message: json_encode($stockInfo));
        }

        return $id;
    }

    /**
     * @param int $userId
     * @return array
     */
    public function getStockSearchLogByUserId(int $userId): array
    {
        $logs = $this->stockLogRepository->findByUserId($userId);

        $response = [];
        foreach ($logs as $log) {
            $response[] = $log['json_response'];
        }

        return $response;
    }
}
