<?php
declare(strict_types=1);

namespace App\Notification\EU;

use App\Notification\Contracts\SmsSenderInterface;
use App\Notification\Infra\HttpGateway;
use Psr\Log\LoggerInterface;

final class EuSmsSender implements SmsSenderInterface
{
    public function __construct(
        private HttpGateway $smsApi,
        private LoggerInterface $logger
    ) {}

    public function send(string $phone, string $text): void
    {
        //TODO Enum Encoding
        $text = mb_strimwidth($text, 0, 480, '…', 'UTF-8');

        $this->smsApi->postJson('/sms/send', [
            'to' => $phone,
            'text' => $text ,
        ]);

        $this->logger->info('EU sms sent', ['to' => $phone]);
    }
}
