<?php

namespace NovaCore\Console\Commands\Mail;

use NovaCore\Console\Command;
use NovaCore\Mail\Queue\QueueManager;

class RetryFailedCommand extends Command
{
    protected string $signature = 'mail:retry {id?}';
    protected string $description = 'Retry failed emails in the queue';

    protected QueueManager $queueManager;

    public function __construct()
    {
        $this->queueManager = new QueueManager();
    }

    public function handle(): void
    {
        try {
            $id = $this->argument('id');
            $driver = $this->queueManager->driver();

            if ($id) {
                $this->info("Retrying failed email ID: {$id}");
                $result = $driver->retry($id);
                
                if ($result) {
                    $this->info("Successfully retried email ID: {$id}");
                } else {
                    $this->error("Failed to retry email ID: {$id}");
                }
            } else {
                $this->info('Retrying all failed emails...');
                $count = $driver->retryAll();
                $this->info("Successfully retried {$count} failed emails.");
            }
        } catch (\Exception $e) {
            $this->error('Failed to retry emails: ' . $e->getMessage());
        }
    }
}
