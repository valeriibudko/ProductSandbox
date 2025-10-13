<?php
declare(strict_types=1);

namespace App\Infrastructure;

use App\Application\VersionStore;
use App\Domain\DocumentSnapshot;

final class InMemoryVersionStore implements VersionStore
{
    /** @var DocumentSnapshot[] */
    private array $undo = [];
    /** @var DocumentSnapshot[] */
    private array $redo = [];

    public function __construct(private readonly int $limit = 50) {}

    public function push(DocumentSnapshot $snapshot): void
    {
        $this->undo[] = $snapshot;
        if (count($this->undo) > $this->limit) {
            // displacing the oldest ones
            array_shift($this->undo);
        }
    }

    public function undo(): ?DocumentSnapshot
    {
        if (!$this->undo) {
            return null;
        }
        $snap = array_pop($this->undo);
        $this->redo[] = $snap;
        return $snap;
    }

    public function redo(): ?DocumentSnapshot
    {
        if (!$this->redo) {
            return null;
        }
        $snap = array_pop($this->redo);
        $this->undo[] = $snap;
        return $snap;
    }

    public function clearRedo(): void
    {
        $this->redo = [];
    }

    public function size(): array
    {
        return ['undo' => count($this->undo), 'redo' => count($this->redo)];
    }
}
