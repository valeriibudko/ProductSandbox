<?php
declare(strict_types=1);

namespace App\Notification\Transport;

use App\Notification\DTO\Message;

interface Transport
{
    /**
     * @throws \RuntimeException on delivery failure
     */
    public function send(Message $message): void;
}
