<?php
declare(strict_types=1);

namespace App\Application\Middleware;

use App\Application\Mediator\Contracts\MiddlewareInterface;

final class NoopTransactionMiddleware implements MiddlewareInterface
{
    public function process(object $message, callable $next): mixed { return $next($message); }
}
