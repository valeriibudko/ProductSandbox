#!/usr/bin/env php
<?php

declare(strict_types=1);

use App\Storage\Decorator\{AuditLogStorageDecorator, ClamAvScanner, RetryStorageDecorator, VirusScanStorageDecorator};
use App\Storage\Document;
use App\Storage\S3DocumentStorage;
use Aws\S3\S3Client;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use App\Application\UploadHandler;
use App\Config\AwsConfig;

require __DIR__ . '/../vendor/autoload.php';

$cfg = AwsConfig::fromIni(__DIR__ . '/../env.ini');

// Logging
$logger = new Logger('main');
$auditLogger = new Logger('audit');
$logger->pushHandler(new StreamHandler('php://stdout', Level::Debug));
$auditLogger->pushHandler(new StreamHandler(__DIR__ . '/../var/log/audit.log', Level::Info));

// Initialize S3 client
$s3Options = [
        'version' => 'latest',
        'region'  => $cfg->region,
        'credentials' => [
                'key'    => $cfg->key,
                'secret' => $cfg->secret,
        ],
];

if ($cfg->endpoint) {
    $s3Options['endpoint'] = $cfg->endpoint;
    $s3Options['use_path_style_endpoint'] = true;
}
$s3 = new S3Client($s3Options);

// Wire storage layers manually
$baseStorage = new S3DocumentStorage($s3, $cfg->bucket, public: $cfg->public);
$virusScanner = new ClamAvScanner();
$virusLayer   = new VirusScanStorageDecorator($baseStorage, $virusScanner);

$retryLayer   = new RetryStorageDecorator($virusLayer, $logger, maxAttempts: 2, sleepMs: 200);
$auditLayer   = new AuditLogStorageDecorator($retryLayer, $auditLogger);

// Final storage (decorated)
$storage = $auditLayer;

// Simulate document upload
$tmpFile = tempnam(sys_get_temp_dir(), 'demo_');
file_put_contents($tmpFile, 'Hello Decorator World!');

$document = new Document(
        originalName: 'hello.txt',
        mimeType: 'text/plain',
        pathOnDisk: $tmpFile
);

// Run upload
$handler = new UploadHandler($storage);

try {
    $url = $handler->handle($document->pathOnDisk, $document->originalName, $document->mimeType);
    echo "\nFile uploaded successfully!\nURL: {$url}\n";
} catch (Throwable $e) {
    echo "\nUpload failed: " . $e->getMessage() . "\n";
}

// Clean up
@unlink($tmpFile);
echo "Demo completed.\n";