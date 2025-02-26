<?php

namespace NovaCore\Upload\Validation;

class FileValidator
{
    private array $allowedTypes;
    private int $maxSize;
    private array $errors = [];

    public function __construct(array $allowedTypes = [], int $maxSize = 0)
    {
        $this->allowedTypes = $allowedTypes;
        $this->maxSize = $maxSize;
    }

    public function validate(array $file): bool
    {
        if (!isset($file['tmp_name']) || !file_exists($file['tmp_name'])) {
            $this->errors[] = 'Yüklenecek dosya bulunamadı';
            return false;
        }

        if (!empty($this->allowedTypes) && !in_array($file['type'], $this->allowedTypes)) {
            $this->errors[] = 'Geçersiz dosya türü';
            return false;
        }

        if ($this->maxSize > 0 && $file['size'] > $this->maxSize) {
            $this->errors[] = sprintf(
                'Dosya boyutu izin verilen boyuttan büyük (Max: %s MB)',
                number_format($this->maxSize / 1024 / 1024, 2)
            );
            return false;
        }

        return true;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function setAllowedTypes(array $types): self
    {
        $this->allowedTypes = $types;
        return $this;
    }

    public function setMaxSize(int $size): self
    {
        $this->maxSize = $size;
        return $this;
    }
}
