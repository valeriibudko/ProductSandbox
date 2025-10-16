<?php
declare(strict_types=1);

namespace App\Notification\EU;

use App\Notification\Contracts\EmailSenderInterface;
use App\Notification\Infra\HttpGateway;
use Psr\Log\LoggerInterface;

final class EuEmailSender implements EmailSenderInterface
{
    public function __construct(
        private HttpGateway $mailApi,
        private LoggerInterface $logger
    ) {}

    public function send(string $to, string $subject, string $html): void
    {
        $html .= '<hr><small>You are receiving this email per GDPR consent. Unsubscribe link inside.</small>';

        $this->mailApi->postJson('/mail/send', [
            'to' => $to,
            'subject' => $subject,
            'html' => $html,
            'headers' => ['X-GDPR-Consent' => 'true'],
        ]);

        $this->logger->info('EU email sent', ['to' => $to, 'subject' => $subject]);
    }
}
