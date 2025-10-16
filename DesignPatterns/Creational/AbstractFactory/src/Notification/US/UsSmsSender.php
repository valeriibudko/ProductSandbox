<?php
declare(strict_types=1);

namespace App\Notification\US;

use App\Notification\Contracts\SmsSenderInterface;
use App\Notification\Infra\HttpGateway;
use Psr\Log\LoggerInterface;

final class UsSmsSender implements SmsSenderInterface
{
    public function __construct(
        private HttpGateway $smsApi,
        private LoggerInterface $logger
    ) {}

    public function send(string $phone, string $text): void
    {
        $this->smsApi->postJson('/sms/send', [
            'to' => $phone,
            'text' => $text,
        ]);

        $this->logger->info('US sms sent', ['to' => $phone]);
    }
}
