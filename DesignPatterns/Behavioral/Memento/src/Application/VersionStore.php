<?php
declare(strict_types=1);

namespace App\Application;

use App\Domain\DocumentSnapshot;

interface VersionStore
{
    /**
     * Save a new snapshot
     */
    public function push(DocumentSnapshot $snapshot): void;

    /**
     * Get a snapshot for undo. Step back
     */
    public function undo(): ?DocumentSnapshot;

    /**
     * Get a snapshot for redo. Step forward
     */
    public function redo(): ?DocumentSnapshot;

    /**
     * Clear the redo stack on new user action
     */
    public function clearRedo(): void;

    /**
     * Current stack sizes for monitoring
     */
    public function size(): array; // ['undo' => int, 'redo' => int]
}
