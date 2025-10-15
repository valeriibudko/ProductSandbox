<?php
declare(strict_types=1);

namespace Tests\Unit;

use App\Application\VersioningService;
use App\Infrastructure\InMemoryVersionStore;
use App\Domain\Document;
use PHPUnit\Framework\TestCase;

final class VersioningServiceTest extends TestCase
{
    public function testCheckpointUndoRedoInMemory(): void
    {
        $doc = new Document('T1', 'B1', ['tags' => ['a']]);
        $store = new InMemoryVersionStore(limit: 10);
        $svc = new VersioningService($store);

        $svc->setCheckpoint($doc, 'init');
        $doc->setTitle('T2');
        $svc->setCheckpoint($doc, 'rename');
        $doc->setBody('B2');
        $svc->setCheckpoint($doc, 'write');

        $this->assertSame(['undo' => 3, 'redo' => 0], $svc->stats());
        $this->assertTrue($svc->undo($doc));
        $this->assertSame('B2', $doc->getBody(), 'undo returns last snapshot state into doc');
        $this->assertTrue($svc->undo($doc));
        $this->assertSame('T2', $doc->getTitle(), 'title after two undos is T2');
        $this->assertTrue($svc->redo($doc));
        $this->assertSame('T2', $doc->getTitle(), 'redo brings us back to previous snapshot');
        $this->assertSame(['undo' => 3, 'redo' => 1], $svc->stats());
    }

    public function testRedoClearedAfterNewCheckpoint(): void
    {
        $doc = new Document('T1', 'B1', []);
        $svc = new VersioningService(new InMemoryVersionStore(10));

        $svc->setCheckpoint($doc, 'init');
        $doc->setTitle('T2'); $svc->setCheckpoint($doc, 'rename');
        $doc->setBody('B2');  $svc->setCheckpoint($doc, 'write');

        $this->assertTrue($svc->undo($doc));
        $this->assertTrue($svc->undo($doc));
        $this->assertSame(['undo' => 1, 'redo' => 2], $svc->stats());

        // A new change after undo for cleaning redo
        $doc->setTitle('T2.1');
        $svc->setCheckpoint($doc, 'minor rename');

        $this->assertSame(['undo' => 2, 'redo' => 0], $svc->stats());
        $this->assertFalse($svc->redo($doc), 'redo should be empty after new checkpoint');
    }

    public function testLimitEvictsOldestSnapshots(): void
    {
        $doc = new Document('T', 'B', []);
        $limit = 3;
        $svc = new VersioningService(new InMemoryVersionStore($limit));

        // 4 checkpoint with limit 3. Older will off
        for ($i = 1; $i <= 4; $i++) {
            $doc->setTitle("T{$i}");
            $svc->setCheckpoint($doc, "step{$i}");
        }

        $this->assertSame(['undo' => $limit, 'redo' => 0], $svc->stats());

        $this->assertTrue($svc->undo($doc));
        $this->assertTrue($svc->undo($doc));
        $this->assertTrue($svc->undo($doc));
        $this->assertFalse($svc->undo($doc), 'oldest snapshot was evicted, no more undo');
    }
}
