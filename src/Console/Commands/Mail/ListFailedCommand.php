<?php

namespace NovaCore\Console\Commands\Mail;

use NovaCore\Console\Command;
use NovaCore\Mail\Queue\QueueManager;

class ListFailedCommand extends Command
{
    protected string $signature = 'mail:failed';
    protected string $description = 'List all failed emails in the queue';

    protected QueueManager $queueManager;

    public function __construct()
    {
        $this->queueManager = new QueueManager();
    }

    public function handle(): void
    {
        try {
            $driver = $this->queueManager->driver();
            $failed = $driver->listFailed();

            if (empty($failed)) {
                $this->info('No failed emails found.');
                return;
            }

            $this->info('Failed Emails:');
            $this->info('-------------');

            foreach ($failed as $mail) {
                $this->info("ID: " . $mail->getId());
                $this->info("To: " . implode(', ', $mail->getTo()));
                $this->info("Subject: " . $mail->getSubject());
                $this->info("Failed At: " . $mail->getFailedAt());
                $this->info("Error: " . $mail->getError());
                $this->info('-------------');
            }
        } catch (\Exception $e) {
            $this->error('Failed to list failed emails: ' . $e->getMessage());
        }
    }
}
