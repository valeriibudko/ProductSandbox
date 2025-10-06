<?php

declare(strict_types=1);

namespace App\Storage;

use Aws\S3\S3Client;
use Ramsey\Uuid\Uuid;

final class S3DocumentStorage implements DocumentStorageInterface
{
    public function __construct(
        private readonly S3Client $s3,
        private readonly string $bucket,
        private readonly bool $public = false
    ) {}

    public function store(Document $document): StoredDocument
    {
        $key = Uuid::uuid4()->toString() . '/' . basename($document->originalName);

        $result = $this->s3->putObject([
            'Bucket'      => $this->bucket,
            'Key'         => $key,
            'SourceFile'  => $document->pathOnDisk,
            'ContentType' => $document->mimeType,
            'ACL'         => $this->public ? 'public-read' : 'private',
            'ChecksumAlgorithm' => 'SHA256',
        ]);

        $url = $this->public
            ? $result['ObjectURL']
            : $this->s3->createPresignedRequest(
                $this->s3->getCommand('GetObject', ['Bucket' => $this->bucket, 'Key' => $key]),
                '+15 minutes'
            )->getUri()->__toString();

        $size = filesize($document->pathOnDisk);
        $checksum = hash_file('sha256', $document->pathOnDisk);

        return new StoredDocument($key, (string)$url, $size ?: 0, $checksum);
    }
}
