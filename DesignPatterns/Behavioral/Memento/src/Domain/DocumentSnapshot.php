<?php
declare(strict_types=1);

namespace App\Domain;

/**
 * A snapshot of the document state. Completely immutable.
 * Only the Document should create such objects code discipline control.
 */
final class DocumentSnapshot
{
    public function __construct(
        private readonly string $id,
        private readonly string $title,
        private readonly string $body,
        private readonly array  $metadata,
        private readonly \DateTimeImmutable $createdAt,
        private readonly string $reason
    ) {}

    public function id(): string { return $this->id; }
    public function title(): string { return $this->title; }
    public function body(): string  { return $this->body; }
    public function metadata(): array { return $this->metadata; }
    public function createdAt(): \DateTimeImmutable { return $this->createdAt; }
    public function reason(): string { return $this->reason; }

    /**
     * For serialization to file storage
     * */
    public function toArray(): array
    {
        return [
            'id'        => $this->id,
            'title'     => $this->title,
            'body'      => $this->body,
            'metadata'  => $this->metadata,
            'createdAt' => $this->createdAt->format(\DateTimeInterface::ATOM),
            'reason'    => $this->reason,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            title: $data['title'],
            body: $data['body'],
            metadata: $data['metadata'],
            createdAt: new \DateTimeImmutable($data['createdAt']),
            reason: $data['reason'] ?? 'unknown'
        );
    }
}
