<?php

namespace NovaCore\Upload;

use NovaCore\Upload\Contracts\StorageInterface;
use NovaCore\Upload\Contracts\QuotaManagerInterface;
use NovaCore\Upload\Storage\LocalStorage;
use NovaCore\Upload\Validation\FileValidator;

class Upload
{
    protected FileValidator $validator;
    protected StorageInterface $storage;
    protected ?QuotaManagerInterface $quotaManager = null;
    protected array $config;
    protected ?UploadedFile $currentFile = null;

    public function __construct(array $config = [])
    {
        $this->config = array_merge([
            'allowed_types' => config('upload.validation.allowed_types', [
                'image/jpeg', 'image/png', 'image/gif', 'image/webp',
                'image/bmp', 'image/svg+xml', 'application/pdf',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            ]),
            'max_size' => config('upload.validation.max_size', 5 * 1024 * 1024),
            'storage_path' => config('upload.storage.path', storage_path('upload'))
        ], $config);

        $this->validator = new FileValidator(
            $this->config['allowed_types'],
            $this->config['max_size']
        );

        $this->storage = new LocalStorage($this->config['storage_path']);
    }

    public function setQuotaManager(QuotaManagerInterface $manager): self
    {
        $this->quotaManager = $manager;
        return $this;
    }

    public function upload($field): ?UploadedFile
    {
        $file = is_array($field) ? $field : ($_FILES[$field] ?? null);
        
        if (!$file) {
            throw new \RuntimeException('Yüklenecek dosya bulunamadı');
        }

        $this->currentFile = new UploadedFile($file);

        if (!$this->validator->validate($file)) {
            throw new \RuntimeException(implode(', ', $this->validator->getErrors()));
        }

        // Kota kontrolü
        if ($this->quotaManager && !$this->quotaManager->checkQuota($file['size'])) {
            throw new \RuntimeException('Depolama alanı sınırına ulaşıldı');
        }

        return $this->currentFile;
    }

    public function allowed(array $mimes): self
    {
        $this->validator->setAllowedTypes($mimes);
        return $this;
    }

    public function onlyImages(): self
    {
        return $this->allowed([
            'image/jpeg', 'image/png', 'image/gif',
            'image/webp', 'image/bmp', 'image/svg+xml'
        ]);
    }

    public function onlyPdf(): self
    {
        return $this->allowed(['application/pdf']);
    }

    public function maxSize(int $size): self
    {
        $this->validator->setMaxSize($size);
        return $this;
    }

    public function to(string $path, ?string $name = null): UploadedFile
    {
        if (!$this->currentFile) {
            throw new \RuntimeException('Önce upload() metodunu çağırmalısınız');
        }

        $fileName = $name ?? uniqid();
        $fullFileName = $fileName . '.' . $this->currentFile->getExtension();

        $relativePath = trim($path, '/');
        
        if (!$this->storage->put(
            $relativePath . '/' . $fullFileName,
            $this->currentFile->getTempPath()
        )) {
            throw new \RuntimeException('Dosya yüklenirken bir hata oluştu');
        }

        $this->currentFile
            ->setStoredName($fullFileName)
            ->setStoredPath($relativePath);

        // Kullanılan alanı güncelle
        if ($this->quotaManager) {
            $this->quotaManager->updateUsedSpace($this->currentFile->getSize());
        }

        return $this->currentFile;
    }

    public function delete(string $path): bool
    {
        return $this->storage->delete($path);
    }

    public function setStorage(StorageInterface $storage): self
    {
        $this->storage = $storage;
        return $this;
    }
}
