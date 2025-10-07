<?php

declare(strict_types=1);

namespace App\Infrastructure\StdoutLogger;

final class StdoutStdoutLogger implements StdoutLoggerInterface
{
    public function info(string $message, array $context = []): void    { $this->log('INFO', $message, $context); }
    public function warning(string $message, array $context = []): void { $this->log('WARN', $message, $context); }
    public function error(string $message, array $context = []): void   { $this->log('ERROR', $message, $context); }

    private function log(string $level, string $message, array $context): void
    {
        $time = (new \DateTimeImmutable())->format('c');
        file_put_contents('php://stdout', sprintf("[%s] %s %s %s\n", $time, $level, $message, json_encode($context)));
    }
}

