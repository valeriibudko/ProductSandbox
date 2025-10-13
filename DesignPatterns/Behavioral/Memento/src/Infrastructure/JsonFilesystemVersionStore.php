<?php
declare(strict_types=1);

namespace App\Infrastructure;

use App\Application\VersionStore;
use App\Domain\DocumentSnapshot;

final class JsonFilesystemVersionStore implements VersionStore
{
    /** @var string path to folder project and versions */
    private string $dir;
    private int $limit;

    /** файлы для стеков */
    private string $undoFile;
    private string $redoFile;

    public function __construct(string $dir, int $limit = 200)
    {
        $this->dir = rtrim($dir, DIRECTORY_SEPARATOR);
        if (!is_dir($this->dir)) {
            if (!mkdir($this->dir, 0777, true) && !is_dir($this->dir)) {
                throw new \RuntimeException("Cannot create dir: {$this->dir}");
            }
        }
        $this->limit = $limit;
        $this->undoFile = $this->dir . DIRECTORY_SEPARATOR . 'undo.json';
        $this->redoFile = $this->dir . DIRECTORY_SEPARATOR . 'redo.json';
        $this->init();
    }

    private function init(): void
    {
        foreach ([$this->undoFile, $this->redoFile] as $file) {
            if (!file_exists($file)) {
                file_put_contents($file, json_encode([]));
            }
        }
    }

    /** @return DocumentSnapshot[] */
    private function readStack(string $file): array
    {
        $raw = file_get_contents($file);
        $data = $raw !== false ? json_decode($raw, true) : [];
        if (!is_array($data)) $data = [];
        return array_map(fn(array $row) => DocumentSnapshot::fromArray($row), $data);
    }

    /** @param DocumentSnapshot[] $stack */
    private function writeStack(string $file, array $stack): void
    {
        $data = array_map(fn(DocumentSnapshot $s) => $s->toArray(), $stack);
        file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    public function push(DocumentSnapshot $snapshot): void
    {
        $undo = $this->readStack($this->undoFile);
        $undo[] = $snapshot;
        if (count($undo) > $this->limit) {
            array_shift($undo);
        }
        $this->writeStack($this->undoFile, $undo);
    }

    public function undo(): ?DocumentSnapshot
    {
        $undo = $this->readStack($this->undoFile);
        if (!$undo) return null;
        $snap = array_pop($undo);
        $redo = $this->readStack($this->redoFile);
        $redo[] = $snap;

        $this->writeStack($this->undoFile, $undo);
        $this->writeStack($this->redoFile, $redo);

        return $snap;
    }

    public function redo(): ?DocumentSnapshot
    {
        $redo = $this->readStack($this->redoFile);
        if (!$redo) return null;
        $snap = array_pop($redo);
        $undo = $this->readStack($this->undoFile);
        $undo[] = $snap;

        $this->writeStack($this->undoFile, $undo);
        $this->writeStack($this->redoFile, $redo);

        return $snap;
    }

    public function clearRedo(): void
    {
        $this->writeStack($this->redoFile, []);
    }

    public function size(): array
    {
        return ['undo' => count($this->readStack($this->undoFile)), 'redo' => count($this->readStack($this->redoFile))];
    }
}
