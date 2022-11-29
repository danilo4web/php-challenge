<?php

namespace App\Infra\Service\Mail;

use Swift_Mailer;
use Swift_Message;

class MailService
{
    /**
     * @param Swift_Mailer $mailer
     */
    public function __construct(private Swift_Mailer $mailer)
    {
    }

    public function sendMessage(string $email, string $stock): ?int
    {
        try {
            $message = (new Swift_Message('Stock Quote: ' . date('Y-m-d H:i:s')))
                ->setFrom(['phpchallenge@test.io' => 'PHP Challenge'])
                ->setTo([$email])
                ->setBody($stock);

            return $this->mailer->send($message);
        } catch (Exception $exception) {
            echo $exception->getMessage() . PHP_EOL;
        }
    }
}
