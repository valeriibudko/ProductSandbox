<?php
declare(strict_types=1);

namespace App\Application\Mediator;

final class SimpleContainer
{
    /** @var array<class-string, callable(self):object> */
    private array $factories = [];

    public function set(string $id, callable $factory): void
    {
        $this->factories[$id] = $factory;
    }

    public function get(string $id): object
    {
        if (!isset($this->factories[$id])) {
            throw new \RuntimeException("No service for {$id}");
        }
        // Simple singleton scope
        static $instances = [];
        return $instances[$id] ??= ($this->factories[$id])($this);
    }
}
