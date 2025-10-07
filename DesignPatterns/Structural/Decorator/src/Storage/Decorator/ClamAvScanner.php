<?php

declare(strict_types=1);

namespace App\Storage\Decorator;

use App\Storage\Document;
use App\Storage\DocumentStorageInterface;
use App\Storage\StoredDocument;
use RuntimeException;

interface VirusScanner
{
    /** Returns true if file is clean, otherwise false (or throw). */
    public function scan(string $pathToFile): bool;
}

final class ClamAvScanner implements VirusScanner
{
    public function __construct(private readonly string $clamSocket = 'tcp://127.0.0.1:3310') {}

    public function scan(string $pathToFile): bool
    {
        // Minimal sketch; in production use a proper client.
        // Here we assume an external daemon scans the file.
        // Return true to simulate "clean".
        return true;
    }
}

final class VirusScanStorageDecorator implements DocumentStorageInterface
{
    public function __construct(
        private readonly DocumentStorageInterface $inner,
        private readonly VirusScanner $scanner
    ) {}

    public function store(Document $document): StoredDocument
    {
        if (!$this->scanner->scan($document->pathOnDisk)) {
            throw new RuntimeException('Virus detected, upload blocked.');
        }
        return $this->inner->store($document);
    }
}
