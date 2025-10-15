<?php
declare(strict_types=1);

namespace Tests\Support;

use Psr\Log\LoggerInterface;

final class FakeLogger implements LoggerInterface
{
    /** @var array<int, array{level:string,message:string,context:array}> */
    public array $records = [];

    public function emergency(\Stringable|string $message, array $context = []): void { $this->log('emergency', $message, $context); }
    public function alert(\Stringable|string $message, array $context = []): void     { $this->log('alert', $message, $context); }
    public function critical(\Stringable|string $message, array $context = []): void  { $this->log('critical', $message, $context); }
    public function error(\Stringable|string $message, array $context = []): void     { $this->log('error', $message, $context); }
    public function warning(\Stringable|string $message, array $context = []): void   { $this->log('warning', $message, $context); }
    public function notice(\Stringable|string $message, array $context = []): void    { $this->log('notice', $message, $context); }
    public function info(\Stringable|string $message, array $context = []): void      { $this->log('info', $message, $context); }
    public function debug(\Stringable|string $message, array $context = []): void     { $this->log('debug', $message, $context); }


    public function log($level, \Stringable|string $message, array $context = []): void
    {
        $this->records[] = [
            'level'   => $level,
            'message' => (string) $message,
            'context' => $context,
        ];
    }
}
