<?php
declare(strict_types=1);

namespace App\Tests\Unit;

use App\Application\DTO\SendNotificationCommandDTO;
use App\Application\Enum\Channel;
use App\Application\Factory\NotificationServiceFactory;
use App\Application\UseCase\SendNotificationHandler;
use App\Notification\DTO\Message;
use App\Notification\Service\NotificationService;
use App\Notification\Transport\Transport;
use PHPUnit\Framework\TestCase;

/**
 * We test that the handler generates a Message and passes it to the service.
 * We use a test factory and a Double service to avoid touching real transports.
 */
final class SendNotificationHandlerTest extends TestCase
{
    public function testHandlePassesCorrectMessageToService(): void
    {
        $spyService = new class extends NotificationService {
            public ?Message $captured = null;

            protected function createTransport(array $config): Transport
            {
                return new class($this) implements Transport {
                    public function __construct(private $outer) {}
                    public function send(Message $message): void { $this->outer->captured = $message; }
                };
            }
        };

        $testFactory = new class($spyService) extends NotificationServiceFactory {
            public function __construct(private NotificationService $svc) { parent::__construct([]); }
            public function create(Channel $channel): NotificationService { return $this->svc; }
        };

        $handler = new SendNotificationHandler($testFactory);

        $sendNotificationCommandDTO = new SendNotificationCommandDTO(
            channel: Channel::EMAIL,
            recipient: 'user@example.com',
            subject: 'Subject A',
            body: 'Body B'
        );

        $handler->handle($sendNotificationCommandDTO);

        $this->assertNotNull($spyService->captured);
        $this->assertSame('user@example.com', $spyService->captured->recipient);
        $this->assertSame('Subject A', $spyService->captured->subject);
        $this->assertSame('Body B', $spyService->captured->body);
    }
}
