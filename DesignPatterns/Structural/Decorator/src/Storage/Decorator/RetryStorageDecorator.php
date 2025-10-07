<?php

declare(strict_types=1);

namespace App\Storage\Decorator;

use App\Storage\Document;
use App\Storage\DocumentStorageInterface;
use App\Storage\StoredDocument;
use Psr\Log\LoggerInterface;
use Throwable;

final class RetryStorageDecorator implements DocumentStorageInterface
{
    public function __construct(
        private readonly DocumentStorageInterface $inner,
        private readonly LoggerInterface $logger,
        private readonly int $maxAttempts = 3,
        private readonly int $sleepMs = 200
    ) {}

    public function store(Document $document): StoredDocument
    {
        $attempt = 0;
        do {
            try {
                return $this->inner->store($document);
            } catch (Throwable $e) {
                $attempt++;
                $this->logger->warning('Storage attempt failed', [
                    'attempt' => $attempt,
                    'error'   => $e->getMessage(),
                ]);
                if ($attempt >= $this->maxAttempts) {
                    throw $e;
                }
                usleep($this->sleepMs * 1000);
            }
        } while ($attempt < $this->maxAttempts);

        // Unreachable
        throw new \RuntimeException('Retry decorator exhausted attempts.');
    }
}
