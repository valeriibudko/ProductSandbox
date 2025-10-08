<?php

declare(strict_types=1);

use App\Application\DTO\SendNotificationCommandDTO;
use App\Application\Enum\Channel;
use App\Application\Factory\NotificationServiceFactory;
use App\Application\UseCase\SendNotificationHandler;

require __DIR__ . '/../vendor/autoload.php';

$channelArg = $argv[1] ?? 'email';
$recipient  = $argv[2] ?? ($channelArg === 'sms' ? '+351912345678' : 'user@example.com');
$subject    = $argv[3] ?? 'Your invoice #12345';
$body       = $argv[4] ?? 'Hi! Your invoice is attached. Thanks for your purchase.';

// TODO Config variable from env file
$config = [
    'email' => [
        'from' => 'billing@yourapp.com',
        'dsn'  => 'smtp://user:pass@smtp.your-app.com:587',
    ],
    'sms' => [
        'api_key'   => 'live_ABC111SECRET',
        'sender_id' => 'YOUR_APP',
    ],
];

$factory = new NotificationServiceFactory($config);
$handler = new SendNotificationHandler($factory);

try {
    $sendNotificationCommandDTO = new SendNotificationCommandDTO(
        channel: Channel::fromString($channelArg),
        recipient: $recipient,
        subject: $subject,
        body: $body
    );

    $handler->handle($sendNotificationCommandDTO);
    echo "Notification sent via: {$channelArg}.\n";
} catch (Throwable $e) {
    fwrite(STDERR, "Error: {$e->getMessage()}\n");
    exit(1);
}
