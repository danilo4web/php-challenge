<?php

declare(strict_types=1);

namespace App\Application\Controllers;

use App\Domain\Service\Contracts\StockQuoteServiceInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class StockQuoteController
{
    /**
     * @param StockQuoteServiceInterface $stockService
     */
    public function __construct(private StockQuoteServiceInterface $stockService)
    {
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function getCurrentStatus(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();

        if (empty($params['q'])) {
            $response->getBody()->write(json_encode(['message' => 'Please inform the stock quote!']));

            return $response->withHeader('Content-type', 'application/json')
                    ->withStatus(422);
        }

        try {
            $currentStockStatus = $this->stockService->get($params['q']);
            $tokenData = $request->getAttribute('token');

            $this->stockService->saveStockSearch($currentStockStatus, $tokenData['id']);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['message' => $e->getMessage()]));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(422);
        }

        $response->getBody()->write(json_encode($currentStockStatus));

        return $response->withHeader('Content-type', 'application/json');
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function stockLogHistory(Request $request, Response $response): Response
    {
        $token = $request->getAttribute('token');
        $stockLogHistory = $this->stockService->getStockSearchLogByUserId($token['id']);

        $response->getBody()->write($this->jsonResponse($stockLogHistory));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    private function jsonResponse(array $response): string
    {
        return str_replace(['\\', '"{', '}"'], ['', '{', '}'], json_encode($response));
    }
}
