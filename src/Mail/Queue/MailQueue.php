<?php

namespace NovaCore\Mail\Queue;

use NovaCore\Mail\Mail;
use NovaCore\Mail\MailException;

class MailQueue
{
    private string $storageDriver;
    private array $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
        $this->storageDriver = $config['queue_driver'] ?? 'file';
    }

    public function push(QueuedMail $mail): bool
    {
        try {
            $data = $mail->toArray();
            
            switch ($this->storageDriver) {
                case 'file':
                    return $this->storeInFile($data);
                case 'database':
                    return $this->storeInDatabase($data);
                case 'redis':
                    return $this->storeInRedis($data);
                default:
                    throw new MailException("Unsupported queue driver: {$this->storageDriver}");
            }
        } catch (\Exception $e) {
            throw new MailException("Failed to queue mail: {$e->getMessage()}");
        }
    }

    public function process(): void
    {
        $pendingMails = $this->getPendingMails();

        foreach ($pendingMails as $mailData) {
            $mail = new QueuedMail($mailData);

            if ($mail->getScheduledFor() && strtotime($mail->getScheduledFor()) > time()) {
                continue;
            }

            try {
                $mail->markAsProcessing();
                $this->updateMail($mail);

                $mailer = new Mail();
                
                // Configure mail
                foreach ($mail->getTo() as $recipient) {
                    $mailer->to($recipient['address'], $recipient['name']);
                }

                foreach ($mail->getCc() as $recipient) {
                    $mailer->cc($recipient['address'], $recipient['name']);
                }

                foreach ($mail->getBcc() as $recipient) {
                    $mailer->bcc($recipient['address'], $recipient['name']);
                }

                foreach ($mail->getAttachments() as $attachment) {
                    $mailer->attach($attachment['path'], $attachment['name']);
                }

                $mailer->subject($mail->getSubject())
                       ->body($mail->getBody(), $mail->isHtml());

                if ($mail->getAltBody()) {
                    $mailer->altBody($mail->getAltBody());
                }

                // Send mail
                $result = $mailer->send();

                if ($result) {
                    $mail->markAsCompleted();
                    $this->updateMail($mail);

                    // Track mail if tracking is enabled
                    if ($mail->getTrackingId()) {
                        $this->trackMail($mail);
                    }
                } else {
                    throw new MailException("Failed to send mail");
                }
            } catch (\Exception $e) {
                $mail->incrementAttempts();
                $mail->markAsFailed($e->getMessage());
                $this->updateMail($mail);

                // Log error
                error_log("Mail queue error: {$e->getMessage()}");
            }
        }
    }

    private function storeInFile(array $data): bool
    {
        $queueDir = $this->config['queue_path'] ?? storage_path('mail/queue');
        if (!is_dir($queueDir)) {
            mkdir($queueDir, 0755, true);
        }

        $filename = $data['id'] . '.json';
        return file_put_contents($queueDir . '/' . $filename, json_encode($data)) !== false;
    }

    private function storeInDatabase(array $data): bool
    {
        // TODO: Implement database storage
        return false;
    }

    private function storeInRedis(array $data): bool
    {
        // TODO: Implement Redis storage
        return false;
    }

    private function getPendingMails(): array
    {
        $mails = [];

        switch ($this->storageDriver) {
            case 'file':
                $queueDir = $this->config['queue_path'] ?? storage_path('mail/queue');
                if (!is_dir($queueDir)) {
                    return [];
                }

                foreach (glob($queueDir . '/*.json') as $file) {
                    $data = json_decode(file_get_contents($file), true);
                    if ($data['status'] === 'pending' || $data['status'] === 'failed') {
                        $mails[] = $data;
                    }
                }
                break;

            case 'database':
                // TODO: Implement database retrieval
                break;

            case 'redis':
                // TODO: Implement Redis retrieval
                break;
        }

        return $mails;
    }

    private function updateMail(QueuedMail $mail): bool
    {
        $data = $mail->toArray();

        switch ($this->storageDriver) {
            case 'file':
                $queueDir = $this->config['queue_path'] ?? storage_path('mail/queue');
                $filename = $data['id'] . '.json';
                return file_put_contents($queueDir . '/' . $filename, json_encode($data)) !== false;

            case 'database':
                // TODO: Implement database update
                return false;

            case 'redis':
                // TODO: Implement Redis update
                return false;

            default:
                return false;
        }
    }

    private function trackMail(QueuedMail $mail): void
    {
        // TODO: Implement mail tracking
        // This could include:
        // - Tracking when the email was opened
        // - Tracking link clicks
        // - Tracking bounces
        // - Tracking delivery status
    }
}
