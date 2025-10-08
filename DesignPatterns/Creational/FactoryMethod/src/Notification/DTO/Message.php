<?php
declare(strict_types=1);

namespace App\Notification\DTO;

final class Message
{
    public function __construct(
        public readonly string $recipient,   // phone number or email
        public readonly string $subject,     // ignored by SMS, used by Email
        public readonly string $body
    ) {}
}
