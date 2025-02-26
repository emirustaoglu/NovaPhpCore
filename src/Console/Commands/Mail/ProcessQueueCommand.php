<?php

namespace NovaCore\Console\Commands\Mail;

use NovaCore\Console\Command;
use NovaCore\Mail\Queue\QueueManager;

class ProcessQueueCommand extends Command
{
    protected string $signature = 'mail:process';
    protected string $description = 'Process the mail queue';

    protected QueueManager $queueManager;

    public function __construct()
    {
        $this->queueManager = new QueueManager();
    }

    public function handle(): void
    {
        try {
            $this->info('Processing mail queue...');

            $driver = $this->queueManager->driver();
            $processedCount = 0;
            $failedCount = 0;
            $maxJobs = 50;

            while ($processedCount < $maxJobs) {
                $mail = $driver->pop();

                if (!$mail) {
                    break;
                }

                try {
                    $this->info("Processing mail ID: " . $mail->getId());
                    $result = $driver->push($mail);
                    
                    if ($result) {
                        $driver->delete($mail->getId());
                        $processedCount++;
                        $this->info("Mail sent successfully.");
                    } else {
                        $driver->failed($mail, "Mail sending failed");
                        $failedCount++;
                        $this->error("Failed to send mail.");
                    }
                } catch (\Exception $e) {
                    $driver->failed($mail, $e->getMessage());
                    $failedCount++;
                    $this->error("Error: " . $e->getMessage());
                }
            }

            $this->info("Queue processing completed.");
            $this->info("Processed: $processedCount");
            $this->info("Failed: $failedCount");

            if ($failedCount > 0) {
                $this->error("Some mails failed to send.");
            }
        } catch (\Exception $e) {
            $this->error("Queue processing failed: " . $e->getMessage());
        }
    }
}
