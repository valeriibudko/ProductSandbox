<?php
declare(strict_types=1);

namespace App\Notification\Contracts;

interface NotificationFactory
{
    public function createEmailSender(): EmailSenderInterface;
    public function createSmsSender(): SmsSenderInterface;
    public function createTemplateEngine(): TemplateEngineInterface;
}
