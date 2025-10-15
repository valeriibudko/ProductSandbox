<?php
declare(strict_types=1);

namespace Tests\Mediator;

use PHPUnit\Framework\TestCase;

use App\Application\Mediator\SimpleContainer;
use App\Application\Mediator\Mediator;

use App\Application\Middleware\ValidationMiddleware;
use App\Application\Middleware\NoopTransactionMiddleware;

use App\Application\User\Command\CreateUserCommand;
use App\Application\User\Command\CreateUserHandler;

use App\Domain\User\UserRepository;
use App\Domain\User\UserValidator;

use App\Infrastructure\Persistence\InMemoryUserRepository;

final class ValidationTest extends TestCase
{
    private Mediator $mediator;

    protected function setUp(): void
    {
        $c = new SimpleContainer();

        $c->set(UserRepository::class, fn() => new InMemoryUserRepository());
        $c->set(UserValidator::class, fn($c) => new UserValidator($c->get(UserRepository::class)));
        $c->set(CreateUserHandler::class, fn($c) => new CreateUserHandler($c->get(UserRepository::class)));

        $handlers = [
            CreateUserCommand::class => CreateUserHandler::class,
        ];

        $middleware = [
            new ValidationMiddleware($c->get(UserValidator::class)),
            new NoopTransactionMiddleware(),
        ];

        $this->mediator = new Mediator($c, $handlers, $middleware);
    }

    public function testInvalidEmail(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->mediator->send(new CreateUserCommand('not-an-email', 'John'));
    }

    public function testDuplicateEmail(): void
    {
        $this->mediator->send(new CreateUserCommand('dup@example.com', 'John'));
        $this->expectException(\DomainException::class);
        $this->mediator->send(new CreateUserCommand('dup@example.com', 'Jane'));
    }

    public function testShortName(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->mediator->send(new CreateUserCommand('ok@example.com', 'J'));
    }
}
