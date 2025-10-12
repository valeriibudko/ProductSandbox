<?php
declare(strict_types=1);

use App\Application\Mediator\Mediator;
use App\Application\Mediator\SimpleContainer;
use App\Application\Mediator\Contracts\MediatorInterface;
use App\Application\Middleware\LoggingMiddleware;
use App\Application\Middleware\TransactionMiddleware;
use App\Application\Middleware\ValidationMiddleware;
use App\Application\User\Command\CreateUserCommand;
use App\Application\User\Command\CreateUserHandler;
use App\Application\User\Query\GetUserByIdQuery;
use App\Application\User\Query\GetUserByIdHandler;
use App\Domain\User\UserRepository;
use App\Domain\User\UserValidator;
use App\Infrastructure\Persistence\PdoConnectionFactory;
use App\Infrastructure\Persistence\PdoUserRepository;
use Psr\Log\LoggerInterface;
use App\Infrastructure\Persistence\InMemoryUserRepository;
use App\Application\Middleware\NoopTransactionMiddleware;

require __DIR__ . '/../vendor/autoload.php';

$userEmail = $argv[1] ?? 'john@example.com';
$userName = $argv[2] ?? 'John Doe';

// DB init
$path = __DIR__ . '/../var/app.db';
$dir = dirname($path);
if (!is_dir($dir)) {
    mkdir($dir, 0777, true);
}

// === Wiring ===
// TODO Use Bootstrap or DI
$container = new SimpleContainer();

$hasSqlite = in_array('sqlite', \PDO::getAvailableDrivers(), true);

// PDO and migrations
if ($hasSqlite) {
    $container->set(PDO::class, fn() => PdoConnectionFactory::sqlite(__DIR__ . '/../var/app.db'));
    $container->set(UserRepository::class, fn($c) => new PdoUserRepository($c->get(PDO::class)));
    $middleware[] = new TransactionMiddleware($container->get(PDO::class));
    $pdo = $container->get(PDO::class);
    $pdo->exec('CREATE TABLE IF NOT EXISTS users (id INTEGER PRIMARY KEY, email TEXT UNIQUE NOT NULL, name TEXT NOT NULL)');
} else {
    $container->set(UserRepository::class, fn() => new InMemoryUserRepository());
    $middleware[] = new NoopTransactionMiddleware();
}

// Infrastructure and domain
$container->set(UserRepository::class, fn($c) => new PdoUserRepository($c->get(PDO::class)));
$container->set(UserValidator::class, fn($c) => new UserValidator($c->get(UserRepository::class)));

// Logger
//TODO use Monolog in production
$container->set(LoggerInterface::class, fn() => new class implements LoggerInterface {
    public function emergency(\Stringable|string $message, array $context = []): void
    {
        $this->log('emergency', $message, $context);
    }
    public function alert(\Stringable|string $message, array $context = []): void
    {
        $this->log('alert', $message, $context);
    }
    public function critical(\Stringable|string $message, array $context = []): void
    {
        $this->log('critical', $message, $context);
    }
    public function error(\Stringable|string $message, array $context = []): void
    {
        $this->log('error', $message, $context);
    }
    public function warning(\Stringable|string $message, array $context = []): void
    {
        $this->log('warning', $message, $context);
    }
    public function notice(\Stringable|string $message, array $context = []): void
    {
        $this->log('notice', $message, $context);
    }
    public function info(\Stringable|string $message, array $context = []): void
    {
        $this->log('info', $message, $context);
    }
    public function debug(\Stringable|string $message, array $context = []): void
    {
        $this->log('debug', $message, $context);
    }
    public function log($level, \Stringable|string $message, array $context = []): void
    {
        $msg = (string)$message;
        error_log("[$level] $msg " . json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }
});

// Storage selection. SQLite if driver available, otherwise InMemory
$hasSqlite = in_array('sqlite', \PDO::getAvailableDrivers(), true);
$txMiddleware = null;

if ($hasSqlite) {
    $container->set(PDO::class, fn() => PdoConnectionFactory::sqlite(__DIR__ . '/../var/app.db'));
    $pdo = $container->get(PDO::class);
    $pdo->exec('CREATE TABLE IF NOT EXISTS users (id INTEGER PRIMARY KEY, email TEXT UNIQUE NOT NULL, name TEXT NOT NULL)');
    $container->set(UserRepository::class, fn($c) => new PdoUserRepository($c->get(PDO::class)));
    $txMiddleware = new TransactionMiddleware($container->get(PDO::class));
} else {
    $container->set(UserRepository::class, fn() => new InMemoryUserRepository());
    $txMiddleware = new NoopTransactionMiddleware();
}

//Service domain
$container->set(UserValidator::class, fn($c) => new UserValidator($c->get(UserRepository::class)));

// Handlers
$container->set(CreateUserHandler::class, fn($c) => new CreateUserHandler($c->get(UserRepository::class)));
$container->set(GetUserByIdHandler::class, fn($c) => new GetUserByIdHandler($c->get(UserRepository::class)));

// Middleware. The order is important: logging -> validation -> transaction
$middleware = [
    new LoggingMiddleware($container->get(LoggerInterface::class)),
    new ValidationMiddleware($container->get(UserValidator::class)),
    $txMiddleware,
];

//Registry "message → handler"
$handlers = [
    CreateUserCommand::class => CreateUserHandler::class,
    GetUserByIdQuery::class => GetUserByIdHandler::class,
];

// Mediator
$container->set(MediatorInterface::class, fn($c) => new Mediator($c, $handlers, $middleware));

/** @var MediatorInterface $mediator */
$mediator = $container->get(MediatorInterface::class);

// Example of work
try {
    $userId = $mediator->send(new CreateUserCommand($userEmail, $userName));
    $user = $mediator->send(new GetUserByIdQuery($userId));

    // In case if script run not from CLI
    if (PHP_SAPI !== 'cli') {
        header('Content-Type: application/json');
    }
    echo json_encode(['createdUserId' => $userId, 'fetchedUser' => $user], JSON_UNESCAPED_UNICODE);
} catch (\Throwable $e) {
    if (PHP_SAPI !== 'cli') {
        http_response_code(400);
        header('Content-Type: application/json');
    }
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
