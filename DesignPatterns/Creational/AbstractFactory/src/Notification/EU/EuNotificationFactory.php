<?php
declare(strict_types=1);

namespace App\Notification\EU;

use App\Notification\Contracts\NotificationFactory;
use App\Notification\Contracts\EmailSenderInterface;
use App\Notification\Contracts\SmsSenderInterface;
use App\Notification\Contracts\TemplateEngineInterface;
use App\Notification\Infra\HttpGateway;
use Psr\Log\LoggerInterface;

final class EuNotificationFactory implements NotificationFactory
{
    public function __construct(
        private HttpGateway $mailApi,
        private HttpGateway $smsApi,
        private LoggerInterface $logger
    ) {}

    public function createEmailSender(): EmailSenderInterface
    {
        return new EuEmailSender($this->mailApi, $this->logger);
    }

    public function createSmsSender(): SmsSenderInterface
    {
        return new EuSmsSender($this->smsApi, $this->logger);
    }

    public function createTemplateEngine(): TemplateEngineInterface
    {
        return new EuTemplateEngine();
    }
}
