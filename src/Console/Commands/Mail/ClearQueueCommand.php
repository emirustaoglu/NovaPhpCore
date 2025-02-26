<?php

namespace NovaCore\Console\Commands\Mail;

use NovaCore\Console\Command;
use NovaCore\Mail\Queue\QueueManager;

class ClearQueueCommand extends Command
{
    protected string $signature = 'mail:clear';
    protected string $description = 'Clear all emails from the mail queue';

    protected QueueManager $queueManager;

    public function __construct()
    {
        $this->queueManager = new QueueManager();
    }

    public function handle(): void
    {
        try {
            $this->info('Clearing mail queue...');
            
            $driver = $this->queueManager->driver();
            $count = $driver->clear();
            
            $this->info("Successfully cleared {$count} emails from the queue.");
        } catch (\Exception $e) {
            $this->error('Failed to clear mail queue: ' . $e->getMessage());
        }
    }
}
