<?php

namespace NovaCore\Upload\Storage;

use NovaCore\Upload\Contracts\StorageInterface;

class LocalStorage implements StorageInterface
{
    private string $basePath;

    public function __construct(string $basePath)
    {
        $this->basePath = rtrim($basePath, '/');
    }

    public function put(string $path, $content): bool
    {
        $fullPath = $this->getFullPath($path);
        $directory = dirname($fullPath);

        if (!is_dir($directory)) {
            if (!$this->makeDirectory($directory)) {
                return false;
            }
        }

        if (is_uploaded_file($content)) {
            return move_uploaded_file($content, $fullPath);
        }

        return file_put_contents($fullPath, $content) !== false;
    }

    public function delete(string $path): bool
    {
        $fullPath = $this->getFullPath($path);
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        return false;
    }

    public function exists(string $path): bool
    {
        return file_exists($this->getFullPath($path));
    }

    public function size(string $path): int
    {
        return filesize($this->getFullPath($path));
    }

    public function makeDirectory(string $path): bool
    {
        return mkdir($this->getFullPath($path), 0777, true);
    }

    private function getFullPath(string $path): string
    {
        return $this->basePath . '/' . ltrim($path, '/');
    }
}
