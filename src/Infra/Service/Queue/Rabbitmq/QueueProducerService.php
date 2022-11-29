<?php

namespace App\Infra\Service\Queue\Rabbitmq;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;

class QueueProducerService
{
    /**
     * @param AMQPChannel $channel
     */
    public function __construct(
        private AMQPChannel $channel
    ) {
    }

    public function produce(string $email, string $message): void
    {
        $this->channel->exchange_declare('email', AMQPExchangeType::FANOUT, false, true, false);
        $this->channel->queue_declare('email', false, true, false, false);
        $this->channel->queue_bind('email', 'email', 'email');

        $this->channel->basic_publish(
            msg: (new AMQPMessage(
                body: json_encode(['email' => $email, 'message' => $message]),
                properties: ['delivery_mode' => 2]
            )),
            exchange: 'email'
        );
        $this->channel->close();
    }
}
