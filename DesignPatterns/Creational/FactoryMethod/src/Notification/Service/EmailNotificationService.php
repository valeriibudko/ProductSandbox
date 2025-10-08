<?php
declare(strict_types=1);

namespace App\Notification\Service;

use App\Notification\Transport\Transport;
use App\Notification\Transport\EmailTransport;

class EmailNotificationService extends NotificationService
{
    protected function createTransport(array $config): Transport
    {
        // TODO Put default values to env file
        $from = $config['from'] ?? 'noreply@example.com';
        $dsn  = $config['dsn']  ?? 'smtp://localhost:25';
        return new EmailTransport($from, $dsn);
    }
}
