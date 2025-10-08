<?php
declare(strict_types=1);

namespace App\Notification\Service;

use App\Notification\Transport\Transport;
use App\Notification\Transport\SmsTransport;

class SmsNotificationService extends NotificationService
{
    protected function createTransport(array $config): Transport
    {
        // TODO Put default values to env file
        $apiKey   = $config['api_key']   ?? throw new \RuntimeException('SMS api_key is required.');
        $senderId = $config['sender_id'] ?? 'ACME';
        return new SmsTransport($apiKey, $senderId);
    }
}
