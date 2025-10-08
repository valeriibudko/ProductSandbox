<?php
declare(strict_types=1);

namespace App\Application\Factory;

use App\Application\Enum\Channel;
use App\Notification\Service\NotificationService;
use App\Notification\Service\EmailNotificationService;
use App\Notification\Service\SmsNotificationService;

class NotificationServiceFactory
{
    /**
     * @param array{
     *   email?: array{from?: string, dsn?: string},
     *   sms?: array{api_key?: string, sender_id?: string}
     * } $config
     */
    public function __construct(private array $config = [])
    {
    }

    public function create(Channel $channel): NotificationService
    {
        return match ($channel) {
            Channel::EMAIL => new EmailNotificationService($this->config['email'] ?? []),
            Channel::SMS   => new SmsNotificationService($this->config['sms'] ?? []),
        };
    }
}
