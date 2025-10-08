<?php
declare(strict_types=1);

namespace App\Notification\Transport;

use App\Notification\DTO\Message;

final readonly class SmsTransport implements Transport
{
    public function __construct(
        private string $apiKey,
        private string $senderId
    ) {}

    public function send(Message $message): void
    {
        if (!preg_match('/^\+?[1-9]\d{7,15}$/', $message->recipient)) {
            throw new \RuntimeException('Invalid phone number.');
        }

        // TODO Call API sms:
        error_log(sprintf(
            '[SMS] KEY=%s FROM=%s TO=%s BODY=%s',
            substr($this->apiKey, 0, 6) . '***',
            $this->senderId,
            $message->recipient,
            $message->body
        ));
    }
}
