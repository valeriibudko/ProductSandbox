<?php
declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\DTO\SendNotificationCommandDTO;
use App\Application\Factory\NotificationServiceFactory;
use App\Notification\DTO\Message;

final readonly class SendNotificationHandler
{
    public function __construct(
        private NotificationServiceFactory $factory
    ) {}

    public function handle(SendNotificationCommandDTO $sendNotificationCommandDTO): void
    {
        $service = $this->factory->create($sendNotificationCommandDTO->channel);

        $service->notify(new Message(
            recipient: $sendNotificationCommandDTO->recipient,
            subject:   $sendNotificationCommandDTO->subject,
            body:      $sendNotificationCommandDTO->body
        ));
    }
}
