<?php
declare(strict_types=1);

namespace App\Domain;

final class Document
{
    private string $title;
    private string $body;
    private array $metadata; // ['author' => '…', 'tags' => ['…']]

    public function __construct(string $title = '', string $body = '', array $metadata = [])
    {
        $this->title = $title;
        $this->body = $body;
        $this->metadata = $metadata;
    }

    public function title(): string { return $this->title; }
    public function body(): string  { return $this->body; }
    public function metadata(): array { return $this->metadata; }

    public function rename(string $title): void { $this->title = $title; }
    public function write(string $body): void   { $this->body = $body; }
    public function setMetadata(array $metadata): void { $this->metadata = $metadata; }

    /**
     * Create a snapshot of the current state
     */
    public function createSnapshot(string $reason = 'manual'): DocumentSnapshot
    {
        return new DocumentSnapshot(
            id: bin2hex(random_bytes(8)),
            title: $this->title,
            body: $this->body,
            metadata: $this->metadata,
            createdAt: new \DateTimeImmutable('now'),
            reason: $reason
        );
    }

    /**
     * Restore state from snapshot
     */
    public function restore(DocumentSnapshot $snapshot): void
    {
        $this->title    = $snapshot->title();
        $this->body     = $snapshot->body();
        $this->metadata = $snapshot->metadata();
    }
}
