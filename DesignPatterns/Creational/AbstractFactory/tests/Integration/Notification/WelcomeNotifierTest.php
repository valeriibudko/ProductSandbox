<?php
declare(strict_types=1);

namespace Tests\Notification;

use App\Notification\WelcomeNotifier;
use App\Notification\EU\EuNotificationFactory;
use App\Notification\Infra\HttpGateway;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Tests\Doubles\MockHttpClient;

final class WelcomeNotifierTest extends TestCase
{
    public function testNotifyUserSendsEmailAndSmsEu(): void
    {
        $psr17 = new Psr17Factory();

        $mailClient = new MockHttpClient();
        $smsClient  = new MockHttpClient();

        $mailGw = new HttpGateway($mailClient, $psr17, 'http://local.test');
        $smsGw  = new HttpGateway($smsClient,  $psr17, 'http://local.test');

        $factory  = new EuNotificationFactory($mailGw, $smsGw, new NullLogger());
        $notifier = new WelcomeNotifier($factory);

        $notifier->notifyUser('user@acme.test', '+4912345678', 'Viktoria');

        // We check that one request was sent to each channel
        $this->assertCount(1, $mailClient->requests);
        $this->assertCount(1, $smsClient->requests);

        $emailReq = $mailClient->requests[0];
        $smsReq   = $smsClient->requests[0];

        // Correct paths and type
        $this->assertSame('POST', $emailReq->getMethod());
        $this->assertSame('/mail/send', $emailReq->getUri()->getPath());
        $this->assertSame('application/json', $emailReq->getHeaderLine('Content-Type'));
//
//        $this->assertSame('POST', $smsReq->getMethod());
//        $this->assertSame('/sms/send', $smsReq->getUri()->getPath());
//        $this->assertSame('application/json', $smsReq->getHeaderLine('Content-Type'));
//
//        // Check the payload
//        $emailPayload = json_decode((string)$emailReq->getBody(), true, 512, JSON_THROW_ON_ERROR);
//        $smsPayload   = json_decode((string)$smsReq->getBody(), true, 512, JSON_THROW_ON_ERROR);
//
//        $this->assertSame('user@acme.test', $emailPayload['to'] ?? null);
//        $this->assertStringContainsString('Welcome, Viktoria!', $emailPayload['subject'] ?? '');
//        $this->assertStringContainsString('Hello, Viktoria!', $emailPayload['html'] ?? '');
//        $this->assertStringContainsString('GDPR', $emailPayload['html'] ?? '');                      // GDPR footer
//        $this->assertSame('true', $emailPayload['headers']['X-GDPR-Consent'] ?? null);
//
//        $this->assertSame('+4912345678', $smsPayload['to'] ?? null);
//        $this->assertStringContainsString('Hi Viktoria, your account is ready.', $smsPayload['text'] ?? '');
//        $this->assertStringContainsString('Reply STOP to opt-out EU', $smsPayload['text'] ?? '');
//
    }
}
