<?php
declare(strict_types=1);

namespace App\Application\Middleware;

use App\Application\Mediator\Contracts\MiddlewareInterface;
use PDO;

final class TransactionMiddleware implements MiddlewareInterface
{
    public function __construct(private PDO $pdo) {}

    public function process(object $message, callable $next): mixed
    {
        $this->pdo->beginTransaction();
        try {
            $result = $next($message);
            $this->pdo->commit();
            return $result;
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
