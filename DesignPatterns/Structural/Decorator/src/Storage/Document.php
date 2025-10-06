<?php

declare(strict_types=1);

namespace App\Storage;

final class Document
{
    public function __construct(
        public readonly string $originalName,
        public readonly string $mimeType,
        public readonly string $pathOnDisk // absolute path to uploaded temp file
    ) {}
}
