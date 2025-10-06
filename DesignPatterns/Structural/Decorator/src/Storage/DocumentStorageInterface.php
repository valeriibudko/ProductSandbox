<?php

declare(strict_types=1);

namespace App\Storage;

interface DocumentStorageInterface
{
    public function store(Document $document): StoredDocument;
}