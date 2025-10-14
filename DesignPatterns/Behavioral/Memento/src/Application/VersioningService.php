<?php
declare(strict_types=1);

namespace App\Application;

use App\Domain\Document;

final class VersioningService
{
    public function __construct(
        private readonly VersionStore $store
    ) {}

    /**
     * Need call on every user change
     */
    public function setCheckpoint(Document $doc, string $reason = 'edit'): void
    {
        $this->store->push($doc->createSnapshot($reason));
        // Any new action "breaks" the redo branch
        $this->store->clearRedo();
    }

    public function undo(Document $doc): bool
    {
        $snap = $this->store->undo();
        if (!$snap) return false;
        $doc->restore($snap);
        return true;
    }

    public function redo(Document $doc): bool
    {
        $snap = $this->store->redo();
        if (!$snap) return false;
        $doc->restore($snap);
        return true;
    }

    public function stats(): array
    {
        return $this->store->size();
    }

    public function isUndo(): bool
    {
       $countUndo = $this->store->size()['undo'] ?? 0;
       return $countUndo !== 0;
    }

}
