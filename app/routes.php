<?php

declare(strict_types=1);

use App\Application\Controllers\HelloController;
use App\Application\Controllers\UserController;
use App\Application\Controllers\AuthController;
use App\Application\Controllers\StockQuoteController;

use Slim\App;

return function (App $app) {
    // unprotected routes
    $app->post('/users', UserController::class . ':register');
    $app->post('/login', AuthController::class . ':login');

    // protected routes
    $app->get('/stock', StockQuoteController::class . ':getCurrentStatus');
    $app->get('/history', StockQuoteController::class . ':stockLogHistory');
};
