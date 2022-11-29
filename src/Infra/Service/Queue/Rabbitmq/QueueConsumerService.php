<?php

namespace App\Infra\Service\Queue\Rabbitmq;

use App\Infra\Service\Mail\MailService;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Exchange\AMQPExchangeType;

class QueueConsumerService
{
    /**
     * @param AMQPChannel $channel
     */
    public function __construct(
        private AMQPChannel $channel,
        private MailService $mailService
    ) {
    }

    public function listen()
    {
        echo "Listening..." . PHP_EOL;

        $this->channel->exchange_declare('email', AMQPExchangeType::FANOUT, false, true, false);
        $this->channel->queue_declare('email', false, true, false, false);
        $this->channel->basic_qos(null, 1, null);

        $this->channel->basic_consume(
            'email',
            '',
            false,
            false,
            false,
            false,
            function ($message) {
                $decodedMessage = json_decode($message->body, true);

                echo $decodedMessage['email'] . ' :: ' . $decodedMessage['message'] . PHP_EOL;
                $this->mailService->sendMessage($decodedMessage['email'], $decodedMessage['message']);

                $channel = $message->delivery_info['channel'];
                $channel->basic_ack($message->delivery_info['delivery_tag']);
            }
        );

        while (count($this->channel->callbacks)) {
            $this->channel->wait();
        }

        echo "Done!" . PHP_EOL;
        $this->channel->close();
    }
}
