<?php
declare(strict_types=1);

namespace App\Notification;

use App\Notification\Contracts\NotificationFactory;

final class WelcomeNotifier
{
    public function __construct(private NotificationFactory $factory) {}

    public function notifyUser(string $email, string $phone, string $userName): void
    {
        $tpl = $this->factory->createTemplateEngine();
        $emailSender = $this->factory->createEmailSender();
        $smsSender = $this->factory->createSmsSender();

        $subject = 'Welcome, ' . $userName . '!';
        $html = $tpl->render('<h1>Hello, {{name}}!</h1><p>Your account is ready.</p>', ['name' => $userName]);

        $emailSender->send($email, $subject, $html);

        $smsText = $tpl->render('Hi {{name}}, your account is ready.', ['name' => $userName]);
        $smsSender->send($phone, strip_tags($smsText));
    }
}
