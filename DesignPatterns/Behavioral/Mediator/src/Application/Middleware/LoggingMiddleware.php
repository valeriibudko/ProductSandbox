<?php
declare(strict_types=1);

namespace App\Application\Middleware;

use App\Application\Mediator\Contracts\MiddlewareInterface;
use Psr\Log\LoggerInterface;

final class LoggingMiddleware implements MiddlewareInterface
{
    public function __construct(private LoggerInterface $logger) {}

    public function process(object $message, callable $next): mixed
    {
        $this->logger->info('Dispatch', ['message' => $message::class, 'payload' => get_object_vars($message)]);
        $result = $next($message);
        $this->logger->info('Handled', ['message' => $message::class, 'result' => is_object($result) ? get_class($result) : $result]);
        return $result;
    }
}
