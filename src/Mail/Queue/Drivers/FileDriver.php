<?php

namespace NovaCore\Mail\Queue\Drivers;

use NovaCore\Mail\Queue\QueueDriverInterface;
use NovaCore\Mail\Queue\QueuedMail;

class FileDriver implements QueueDriverInterface
{
    protected array $config;
    protected string $path;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->path = $config['path'] ?? storage_path('framework/queue/emails');
        
        if (!is_dir($this->path)) {
            mkdir($this->path, 0755, true);
        }
    }

    public function push(QueuedMail $mail): bool
    {
        $id = $mail->getId();
        $data = [
            'mail' => $mail,
            'attempts' => 0,
            'status' => 'pending',
            'created_at' => time()
        ];

        $filename = $this->getFilename($id);
        return file_put_contents($filename, serialize($data)) !== false;
    }

    public function pop(): ?QueuedMail
    {
        $files = glob($this->path . '/*.mail');
        
        foreach ($files as $file) {
            $data = unserialize(file_get_contents($file));
            
            if ($data['status'] !== 'pending') {
                continue;
            }

            if (isset($data['mail']->scheduledFor) && 
                strtotime($data['mail']->scheduledFor) > time()) {
                continue;
            }

            // Update status and attempts
            $data['status'] = 'processing';
            $data['attempts']++;
            $data['last_attempt'] = time();
            file_put_contents($file, serialize($data));

            return $data['mail'];
        }

        return null;
    }

    public function delete(string $id): bool
    {
        $filename = $this->getFilename($id);
        return @unlink($filename);
    }

    public function clear(): bool
    {
        $files = glob($this->path . '/*.mail');
        foreach ($files as $file) {
            @unlink($file);
        }
        return true;
    }

    public function count(): int
    {
        return count(glob($this->path . '/*.mail'));
    }

    public function failed(QueuedMail $mail, string $error): bool
    {
        $filename = $this->getFilename($mail->getId());
        
        if (!file_exists($filename)) {
            return false;
        }

        $data = unserialize(file_get_contents($filename));
        $data['status'] = 'failed';
        $data['error'] = $error;
        $data['failed_at'] = time();

        return file_put_contents($filename, serialize($data)) !== false;
    }

    public function retry(string $id): bool
    {
        $filename = $this->getFilename($id);
        
        if (!file_exists($filename)) {
            return false;
        }

        $data = unserialize(file_get_contents($filename));
        $data['status'] = 'pending';
        $data['attempts'] = 0;
        $data['error'] = null;
        $data['failed_at'] = null;

        return file_put_contents($filename, serialize($data)) !== false;
    }

    protected function getFilename(string $id): string
    {
        return $this->path . '/' . $id . '.mail';
    }
}
