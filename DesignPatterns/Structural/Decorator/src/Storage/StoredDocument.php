<?php

declare(strict_types=1);

namespace App\Storage;

final class StoredDocument
{
    public function __construct(
        public readonly string $id,          // storage key
        public readonly string $url,         // public or signed URL
        public readonly int $sizeBytes,
        public readonly string $checksum
    ) {}
}