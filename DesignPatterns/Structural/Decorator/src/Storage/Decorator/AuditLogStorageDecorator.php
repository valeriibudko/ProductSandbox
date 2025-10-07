<?php

declare(strict_types=1);

namespace App\Storage\Decorator;

use App\Storage\Document;
use App\Storage\DocumentStorageInterface;
use App\Storage\StoredDocument;
use Psr\Log\LoggerInterface;

final class AuditLogStorageDecorator implements DocumentStorageInterface
{
    public function __construct(
        private readonly DocumentStorageInterface $inner,
        private readonly LoggerInterface $auditLogger // bind to a channel
    ) {}

    public function store(Document $document): StoredDocument
    {
        $stored = $this->inner->store($document);

        $this->auditLogger->info('Document stored', [
            'original_name' => $document->originalName,
            'mime'          => $document->mimeType,
            'size'          => $stored->sizeBytes,
            'checksum'      => $stored->checksum,
            'id'            => $stored->id,
            'url'           => $stored->url,
        ]);

        return $stored;
    }
}
