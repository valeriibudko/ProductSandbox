<?php
declare(strict_types=1);

namespace Tests\Unit\Mediator;

use PHPUnit\Framework\TestCase;
use Tests\Support\FakeLogger;

use App\Application\Mediator\SimpleContainer;
use App\Application\Mediator\Mediator;
use App\Application\Mediator\Contracts\MediatorInterface;

use App\Application\Middleware\LoggingMiddleware;
use App\Application\Middleware\ValidationMiddleware;
use App\Application\Middleware\NoopTransactionMiddleware;

use App\Application\User\Command\CreateUserCommand;
use App\Application\User\Command\CreateUserHandler;
use App\Application\User\Query\GetUserByIdQuery;
use App\Application\User\Query\GetUserByIdHandler;

use App\Domain\User\UserRepository;
use App\Domain\User\UserValidator;

use App\Infrastructure\Persistence\InMemoryUserRepository;

final class CreateAndFetchUserTest extends TestCase
{
    private MediatorInterface $mediator;
    private FakeLogger $logger;

    protected function setUp(): void
    {
        $c = new SimpleContainer();

        // Infrastructure
        $c->set(UserRepository::class, fn() => new InMemoryUserRepository());
        $c->set(UserValidator::class, fn($c) => new UserValidator($c->get(UserRepository::class)));

        // Handlers
        $c->set(CreateUserHandler::class, fn($c) => new CreateUserHandler($c->get(UserRepository::class)));
        $c->set(GetUserByIdHandler::class, fn($c) => new GetUserByIdHandler($c->get(UserRepository::class)));

        // Middleware
        $this->logger = new FakeLogger();
        $middleware = [
            new LoggingMiddleware($this->logger),
            new ValidationMiddleware($c->get(UserValidator::class)),
            new NoopTransactionMiddleware(),
        ];

        // Реестр обработчиков
        $handlers = [
            CreateUserCommand::class => CreateUserHandler::class,
            GetUserByIdQuery::class  => GetUserByIdHandler::class,
        ];

        $this->mediator = new Mediator($c, $handlers, $middleware);
    }

    public function testCreateAndFetch(): void
    {
        $id = $this->mediator->send(new CreateUserCommand('john@example.com', 'John Doe'));
        $this->assertSame(1, $id);

        $user = $this->mediator->send(new GetUserByIdQuery($id));
        $this->assertIsArray($user);
        $this->assertSame(['id' => 1, 'email' => 'john@example.com', 'name' => 'John Doe'], $user);

        // Логгер получил записи «Dispatch/Handled» минимум по 2 сообщения
        $this->assertGreaterThanOrEqual(2, \count($this->logger->records));
        $classes = \array_column($this->logger->records, 'message');
        $this->assertTrue(\in_array(CreateUserCommand::class, $classes, true));
        $this->assertTrue(\in_array(GetUserByIdQuery::class, $classes, true));
    }
}
