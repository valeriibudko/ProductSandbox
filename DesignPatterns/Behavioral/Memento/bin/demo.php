#!/usr/bin/env php
<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Domain\Document;
use App\Application\VersioningService;
use App\Infrastructure\InMemoryVersionStore;
use App\Infrastructure\JsonFilesystemVersionStore;

$argvList = ['file', 'memory'];
$typeInfrastructure = $argv[1] ?? 'file';
if (!in_array($typeInfrastructure, $argvList)) {
    echo "You need set argv: " . implode(', ', $argvList);
    return false;
}

if ($typeInfrastructure === 'file') {
    $limitUndo = 100;
    $store = new JsonFilesystemVersionStore(__DIR__ . '/../var/versions', limit: $limitUndo);
} else {
    $limitUndo = 10;
    $store = new InMemoryVersionStore(limit: $limitUndo);
}

$doc = new Document('It is a title. Deep 0', 'Hello world', ['author' => 'Alice', 'tags' => ['demo']]);
$versioning = new VersioningService($store);

// First checkpoint. Initial state
$versioning->setCheckpoint($doc, 'Init document');

$doc->setTitle('Deep 1');
$versioning->setCheckpoint($doc, 'Rename title');

$doc->setTitle('Deep 2');
$doc->setBody("Edit body v2");
$versioning->setCheckpoint($doc, 'Write body');

$doc->setTitle('Deep 3');
$doc->setMetadata(['author' => 'Alice', 'tags' => ['demo', 'test']]);
$versioning->setCheckpoint($doc, 'Update metadata');


echo "================================\n";
echo "Current state\n";
echo "================================\n";
echo "ID: {$doc->getId()}\n";
echo "Title: {$doc->getTitle()}\n";
echo "Body: {$doc->getBody()}\n";
echo "Tags: " . implode(', ', $doc->getMetadata()['tags']) . "\n";


$index = 1;
while ($versioning->isUndo() && $index < $limitUndo) {
    echo "================================\n";
    echo "Undo #$index\n";
    $versioning->undo($doc);
    echo "----------------------\n";
    echo "ID: {$doc->getId()}\n";
    echo "Title: {$doc->getTitle()}\n";
    echo "Body: {$doc->getBody()}\n";
    echo "Tags: " . implode(', ', $doc->getMetadata()['tags']) . "\n";
    echo "----------------------\n";
    echo "Stats of versioning:\n";
    print_r($versioning->stats());
    $index++;
}

echo "================================\n";
echo "Redo...\n";
echo "================================\n";
$versioning->redo($doc);
print_r($doc->toArray());
echo "Stats of versioning:\n";
print_r($versioning->stats());
