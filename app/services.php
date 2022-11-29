<?php
declare(strict_types=1);

use App\Domain\Entity\StockQuote;
use App\Domain\Entity\User;
use App\Domain\Repository\StockQuoteRepositoryInterface;
use App\Domain\Repository\UserRepositoryInterface;
use App\Domain\Service\AuthService;
use App\Domain\Service\Contracts\AuthServiceInterface;
use App\Domain\Service\Contracts\StockQuoteServiceInterface;
use App\Domain\Service\Contracts\UserServiceInterface;
use App\Domain\Service\StockQuoteService;
use App\Domain\Service\UserService;
use App\Infra\Gateway\StooqApiGateway;
use App\Infra\Repository\StockQuoteRepository;
use App\Infra\Repository\UserRepository;
use App\Infra\Service\Queue\Rabbitmq\QueueProducerService;
use DI\ContainerBuilder;
use Doctrine\Common\Cache\Psr6\DoctrineProvider;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use PhpAmqpLib\Channel\AMQPChannel;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([

        Swift_Mailer::class => function () {
            $host = $_ENV['MAILER_HOST'] ?? 'smtp.mailtrap.io';
            $port = intval($_ENV['MAILER_PORT']) ?? 465;
            $username = $_ENV['MAILER_USERNAME'] ?? 'test';
            $password = $_ENV['MAILER_PASSWORD'] ?? 'test';

            $transport = (new Swift_SmtpTransport($host, $port))
                ->setUsername($username)
                ->setPassword($password);

            return new Swift_Mailer($transport);
        },

        EntityManager::class => function (): EntityManager {
            $devMode = $_ENV['ENV'] === 'DEV';
            $cacheDirectory = __DIR__ . '/../var/cache/doctrine';
            $metadataDirs = [__DIR__ . '/../src/Domain/Entity'];

            $cache = $devMode ?
                DoctrineProvider::wrap(new ArrayAdapter()) :
                DoctrineProvider::wrap(new FilesystemAdapter(directory: $cacheDirectory));

            $config = Setup::createAttributeMetadataConfiguration(
                $metadataDirs,
                $devMode,
                null,
                $cache
            );

            return EntityManager::create([
                'driver' => $_ENV['DB_DRIVER'],
                'host' => $_ENV['DB_HOST'],
                'port' => $_ENV['DB_PORT'],
                'dbname' => $_ENV['DB_DATABASE'],
                'user' => $_ENV['DB_USERNAME'],
                'password' => $_ENV['DB_PASSWORD'],
                'charset' => $_ENV['DB_CHARSET']
            ], $config);
        },

        AMQPChannel::class => function () {
            $connection = new \PhpAmqpLib\Connection\AMQPStreamConnection(
                $_ENV['RMQ_HOST'],
                $_ENV['RMQ_PORT'],
                $_ENV['RMQ_USERNAME'],
                $_ENV['RMQ_PASSWORD'],
                $_ENV['RMQ_VHOST']
            );

            return $connection->channel();
        },

        AuthServiceInterface::class => function(UserRepositoryInterface $userRepository, User $userEntity): AuthService {
            return new AuthService($userRepository, $userEntity);
        },

        UserRepositoryInterface::class => function(EntityManager $entityManager): UserRepository {
            return new UserRepository($entityManager);
        },

        StockQuoteRepositoryInterface::class => function(EntityManager $entityManager): StockQuoteRepository {
            return new StockQuoteRepository($entityManager);
        },

        UserServiceInterface::class => function(
            UserRepositoryInterface $userRepository,
            User $userEntity
        ): UserService {
            return new UserService($userRepository, $userEntity);
        },

        QueueProducerService::class => function(
            AMQPChannel $channel
        ): QueueProducerService {
            return new QueueProducerService($channel);
        },

        StockQuoteServiceInterface::class => function(
            StooqApiGateway               $stooqApi,
            StockQuoteRepositoryInterface $stockLogRepository,
            StockQuote                    $stockLogEntity,
            UserService                   $userService,
            QueueProducerService          $queueProducerService
        ): StockQuoteService {
            return new StockQuoteService($stooqApi, $stockLogRepository, $stockLogEntity, $userService, $queueProducerService);
        },
    ]);
};
