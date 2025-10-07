<?php
declare(strict_types=1);

namespace App\Application;

use App\Storage\Document;
use App\Storage\DocumentStorageInterface;

final class UploadHandler
{
    public function __construct(private readonly DocumentStorageInterface $storage) {}

    public function handle(string $tmpPath, string $originalName, string $mime): string
    {
        $stored = $this->storage->store(new Document($originalName, $mime, $tmpPath));
        return $stored->url;
    }
}
