<?php
declare(strict_types=1);

namespace App\Tests;

use App\Service\Logger;
use PHPUnit\Framework\TestCase;

final class LoggerTest extends TestCase
{
    private string $tmpDir;

    protected function setUp(): void
    {
        // TODO Check on different location
//        Logger::__resetForTests();
//        $this->tmpDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'logger_test_' . uniqid();
//        mkdir($this->tmpDir, 0777, true);
    }

    protected function tearDown(): void
    {
        // TODO Check on different location
//        $logFile = $this->tmpDir . DIRECTORY_SEPARATOR . 'app.log';
//        if (is_file($logFile)) { @unlink($logFile); }
//        if (is_dir($this->tmpDir)) { @rmdir($this->tmpDir); }
        Logger::__resetForTests();
    }

    public function testSingletonReturnsSameInstance(): void
    {
        $a = Logger::getInstance();
        $b = Logger::getInstance();
        $this->assertSame($a, $b);
    }

    public function testWritesLogEntry(): void
    {
        $logger = Logger::getInstance();
        $logger->info('Application started');

        $logFile = $logger->__getLogFileForTests();
        $this->assertFileExists($logFile);
        $content = file_get_contents($logFile);
        $this->assertStringContainsString('[INFO] Application started', $content);
    }

    public function testContextIsJsonEncoded(): void
    {
        $logger = Logger::getInstance();
        $logger->error('DB failed', ['host' => 'localhost', 'code' => 500]);

        $content = file_get_contents($logger->__getLogFileForTests());
        $this->assertStringContainsString('"host":"localhost"', $content);
        $this->assertStringContainsString('"code":500', $content);
    }

    public function testCustomLevelsAreUppercased(): void
    {
        $logger = Logger::getInstance();
        $logger->log('custom', 'Something happened');

        $content = file_get_contents($logger->__getLogFileForTests());
        $this->assertStringContainsString('[CUSTOM] Something happened', $content);
    }

    public function testCloneAndUnserializeAreForbidden(): void
    {
        $logger = Logger::getInstance();
        $this->expectException(\Error::class);
        /** @phpstan-ignore-next-line */
        $clone = clone $logger;
    }

    public function testUnserializeThrows(): void
    {
        $logger = Logger::getInstance();
        $this->expectException(\RuntimeException::class);
        /** @var Logger $u */
        $u = unserialize(serialize($logger)); // Call __wakeup()
    }
}
