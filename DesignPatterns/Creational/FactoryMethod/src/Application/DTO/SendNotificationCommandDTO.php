<?php
declare(strict_types=1);

namespace App\Application\DTO;

use App\Application\Enum\Channel;

final readonly class SendNotificationCommandDTO
{
    public function __construct(
        public Channel $channel,
        public string  $recipient,
        public string  $subject,
        public string  $body
    ) {}
}
