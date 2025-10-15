<?php
declare(strict_types=1);

namespace Tests\Unit;

use App\Application\VersioningService;
use App\Infrastructure\JsonFilesystemVersionStore;
use App\Domain\Document;
use PHPUnit\Framework\TestCase;

final class JsonFilesystemVersionStoreTest extends TestCase
{
    private string $dir;

    protected function setUp(): void
    {
        $this->dir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'memento_tests_' . bin2hex(random_bytes(4));
        @mkdir($this->dir);
    }

    protected function tearDown(): void
    {
        $it = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->dir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($it as $file) {
            $file->isDir() ? @rmdir($file->getRealPath()) : @unlink($file->getRealPath());
        }
        @rmdir($this->dir);
    }

    public function testPersistAndReloadStacks(): void
    {
        $store = new JsonFilesystemVersionStore($this->dir, limit: 5);
        $svc = new VersioningService($store);

        $doc = new Document('A', 'B', ['tags' => ['x']]);
        $svc->setCheckpoint($doc, 'init');

        $doc->setTitle('A1');
        $svc->setCheckpoint($doc, 'rename');
        $doc->setBody('B1');
        $svc->setCheckpoint($doc, 'write');
        $this->assertSame(
            ['undo' => 3, 'redo' => 0],
            $svc->stats()
        );

        // Reload store. Emulate a new process
        $store2 = new JsonFilesystemVersionStore($this->dir, limit: 5);
        $svc2 = new VersioningService($store2);

        $this->assertSame(
            ['undo' => 3, 'redo' => 0],
            $svc2->stats(),
            'stacks persisted to disk'
        );
        $this->assertTrue($svc2->undo($doc));
        $this->assertTrue($svc2->undo($doc));
        $this->assertSame('A', $doc->getTitle());
        $this->assertSame(['undo' => 0, 'redo' => 3], $svc2->stats());
    }
}
