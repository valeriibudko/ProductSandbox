<?php
declare(strict_types=1);

namespace App\Notification\Transport;

use App\Notification\DTO\Message;

final readonly class EmailTransport implements Transport
{
    public function __construct(
        private string $fromAddress,
        private string $dsn
    ) {}

    public function send(Message $message): void
    {
        if (!str_contains($message->recipient, '@')) {
            throw new \RuntimeException('Invalid email address.');
        }

        // TODO Send with real SMTP/Mail
        error_log(sprintf(
            '[EMAIL] DSN=%s FROM=%s TO=%s SUBJECT=%s BODY=%s',
            $this->dsn,
            $this->fromAddress,
            $message->recipient,
            $message->subject,
            $message->body
        ));
    }
}
