<?php
declare(strict_types=1);

namespace App\Domain;

final class Document
{
    private string $id;
    private string $title;
    private string $body;
    private array $metadata; // ['author' => '…', 'tags' => ['…']]

    public function __construct(string $title = '', string $body = '', array $metadata = [])
    {

        $this->title = $title;
        $this->body = $body;
        $this->metadata = $metadata;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function setBody(string $body): void
    {
        $this->body = $body;
    }

    public function setMetadata(array $metadata): void
    {
        $this->metadata = $metadata;
    }

    /**
     * Create a snapshot of the current state
     */
    public function createSnapshot(string $reason = 'manual'): DocumentSnapshot
    {
        $this->id = bin2hex(random_bytes(8));
        return new DocumentSnapshot(
//            id: bin2hex(random_bytes(8)),
            id: $this->id,
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
        $this->id = $snapshot->getId();
        $this->title = $snapshot->getTitle();
        $this->body = $snapshot->getBody();
        $this->metadata = $snapshot->getMetadata();
    }

    public function toArray(): array
    {
        return [
            'id'     => $this->id,
            'title'     => $this->title,
            'body'      => $this->body,
            'metadata'  => $this->metadata,
        ];
    }
}
