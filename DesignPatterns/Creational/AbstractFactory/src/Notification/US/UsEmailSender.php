<?php
declare(strict_types=1);

namespace App\Notification\US;

use App\Notification\Contracts\EmailSenderInterface;
use App\Notification\Infra\HttpGateway;
use Psr\Log\LoggerInterface;

final class UsEmailSender implements EmailSenderInterface
{
    public function __construct(
        private HttpGateway $mailApi,
        private LoggerInterface $logger
    ) {}

    public function send(string $to, string $subject, string $html): void
    {
        $html .= '<hr><small>To stop receiving emails, click unsubscribe. CAN-SPAM compliant.</small>';

        $this->mailApi->postJson('/mail/send', [
            'to' => $to,
            'subject' => $subject,
            'html' => $html,
        ]);

        $this->logger->info('US email sent', ['to' => $to, 'subject' => $subject]);
    }
}
