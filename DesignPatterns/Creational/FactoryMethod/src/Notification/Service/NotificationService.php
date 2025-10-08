<?php
declare(strict_types=1);

namespace App\Notification\Service;

use App\Notification\DTO\Message;
use App\Notification\Transport\Transport;

/**
 * Creator base class.
 * The factory method `createTransport()` is deferred to subclasses.
 */
abstract class NotificationService
{
    public function __construct(private readonly array $config = [])
    {
    }

    final public function notify(Message $message): void
    {
        $transport = $this->createTransport($this->config);
        $transport->send($message);
    }

    /**
     * Factory Method.
     * Subclasses know which concrete Transport to instantiate and how to configure it.
     */
    abstract protected function createTransport(array $config): Transport;
}
