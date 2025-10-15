<?php
declare(strict_types=1);

namespace App\Service;

final class Logger
{
    private static ?self $instance = null;

    private string $logFile;

    private function __construct(string $fileLog)
    {
        $logDir = __DIR__ . '/../../var/logs/';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0775, true);
        }

        $this->logFile = $logDir . $fileLog;
    }

    /**
     * Return single example class
     */
    public static function getInstance(string $fileLog = 'app.log'): self
    {
        if (self::$instance === null) {
            self::$instance = new self($fileLog);
        }
        return self::$instance;
    }

    /**
     * Record logs
     */
    public function log(string $level, string $message, array $context = []): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $contextString = $context ? json_encode($context, JSON_UNESCAPED_UNICODE) : '';
        $entry = sprintf("[%s] [%s] %s %s\n", $timestamp, strtoupper($level), $message, $contextString);
        file_put_contents($this->logFile, $entry, FILE_APPEND);
    }

    /**
     * Simple method for different levels
     */
    public function info(string $message, array $context = []): void
    {
        $this->log('info', $message, $context);
    }

    public function warning(string $message, array $context = []): void
    {
        $this->log('warning', $message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        $this->log('error', $message, $context);
    }

    private function __clone() { }
    public function __wakeup() { throw new \RuntimeException("Cannot unserialize singleton"); }

    /**
     * Needs only for auto tests.
     * Reset Singleton between test cases
     */
    public static function __resetForTests(): void
    {
        self::$instance = null;
    }

    /**
     * Needs only for auto tests.
     */
    public function __getLogFileForTests(): string
    {
        return $this->logFile;
    }
}
