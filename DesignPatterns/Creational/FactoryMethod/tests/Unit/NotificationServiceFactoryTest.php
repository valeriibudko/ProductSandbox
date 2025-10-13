<?php
declare(strict_types=1);

namespace Tests\Unit;

use App\Application\Enum\Channel;
use App\Application\Factory\NotificationServiceFactory;
use App\Notification\Service\EmailNotificationService;
use App\Notification\Service\NotificationService;
use App\Notification\Service\SmsNotificationService;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

final class NotificationServiceFactoryTest extends TestCase
{

    protected array $config;

    protected function setUp(): void
    {
        $this->config = [
            'email' => ['from' => 'billing@acme.test', 'dsn' => 'smtp://smtp.acme:587'],
            'sms' => ['api_key' => 'k', 'sender_id' => 'ACME'],
        ];
    }

    public function testCreateReturnsEmailServiceWithConfig(): void
    {
        $factory = new NotificationServiceFactory($this->config);

        $service = $factory->create(Channel::EMAIL);
        $this->assertInstanceOf(EmailNotificationService::class, $service);

        // Check that the config actually got inside the base one NotificationService::$config
        $ref = new ReflectionClass(NotificationService::class);
        $prop = $ref->getProperty('config');
        $prop->setAccessible(true);
        $this->assertSame($this->config['email'], $prop->getValue($service));
    }

    public function testCreateReturnsSmsServiceWithConfig(): void
    {
        $factory = new NotificationServiceFactory($this->config);

        $service = $factory->create(Channel::SMS);
        $this->assertInstanceOf(SmsNotificationService::class, $service);

        $ref = new ReflectionClass(NotificationService::class);
        $prop = $ref->getProperty('config');
        $prop->setAccessible(true);
        $this->assertSame($this->config['sms'], $prop->getValue($service));
    }
}
