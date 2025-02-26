<?php

namespace NovaCore\Upload;

class UploadedFile
{
    private array $file;
    private ?string $storedName = null;
    private ?string $storedPath = null;

    public function __construct(array $file)
    {
        $this->file = $file;
    }

    public function getOriginalName(): string
    {
        return $this->file['name'];
    }

    public function getSize(): int
    {
        return $this->file['size'];
    }

    public function getMimeType(): string
    {
        return $this->file['type'];
    }

    public function getExtension(): string
    {
        return pathinfo($this->file['name'], PATHINFO_EXTENSION);
    }

    public function getTempPath(): string
    {
        return $this->file['tmp_name'];
    }

    public function setStoredName(string $name): self
    {
        $this->storedName = $name;
        return $this;
    }

    public function getStoredName(): ?string
    {
        return $this->storedName;
    }

    public function setStoredPath(string $path): self
    {
        $this->storedPath = $path;
        return $this;
    }

    public function getStoredPath(): ?string
    {
        return $this->storedPath;
    }

    public function getFullStoredPath(): ?string
    {
        if ($this->storedPath && $this->storedName) {
            return rtrim($this->storedPath, '/') . '/' . $this->storedName;
        }
        return null;
    }
}
