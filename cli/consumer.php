<?php
require __DIR__ . '/../vendor/autoload.php';

use DI\ContainerBuilder;
use Symfony\Component\Console\Output\ConsoleOutput;
use App\Domain\Mail\Service\MailService;

$dotenv = new \Symfony\Component\Dotenv\Dotenv();
$dotenv->load(__DIR__ . '/../.env');

$containerBuilder = new ContainerBuilder();
$services = require_once __DIR__ . '/../app/services.php';
$services($containerBuilder);

$container = $containerBuilder->build();
$mailService = new \App\Infra\Service\Mail\MailService($container->get(Swift_Mailer::class));

$connection = new \PhpAmqpLib\Connection\AMQPStreamConnection(
    $_ENV['RMQ_HOST'],
    $_ENV['RMQ_PORT'],
    $_ENV['RMQ_USERNAME'],
    $_ENV['RMQ_PASSWORD'],
    $_ENV['RMQ_VHOST']
);

$consumer = new \App\Infra\Service\Queue\Rabbitmq\QueueConsumerService((new \PhpAmqpLib\Channel\AMQPChannel($connection)), $mailService);
$consumer->listen();
