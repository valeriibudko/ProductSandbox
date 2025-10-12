<?php
declare(strict_types=1);

namespace App\Application\Mediator\Contracts;

interface MiddlewareInterface
{
    /**
     * @param callable(object $message): mixed $next
     * @return mixed
     */
    public function process(object $message, callable $next);
}
