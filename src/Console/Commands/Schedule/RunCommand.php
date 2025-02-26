<?php

namespace NovaCore\Console\Commands\Schedule;

use NovaCore\Console\Command;
use NovaCore\Console\Scheduling\Schedule;

class RunCommand extends Command
{
    protected string $signature = 'schedule:run';
    protected string $description = 'Run the scheduled commands';

    protected Schedule $schedule;

    public function __construct()
    {
        $this->schedule = new Schedule();
        $this->loadSchedule();
    }

    public function handle(): void
    {
        try {
            $this->info('Running scheduled commands...');

            $events = $this->schedule->dueEvents();

            if (empty($events)) {
                $this->info('No scheduled commands are due.');
                return;
            }

            foreach ($events as $event) {
                try {
                    $event->run();
                } catch (\Exception $e) {
                    $this->error('Failed to run scheduled command: ' . $e->getMessage());
                }
            }

            $this->info('Scheduled commands completed.');
        } catch (\Exception $e) {
            $this->error('Schedule run failed: ' . $e->getMessage());
        }
    }

    protected function loadSchedule(): void
    {
        $scheduleFile = getcwd() . '/app/Console/Schedule.php';
        
        if (file_exists($scheduleFile)) {
            require $scheduleFile;
            
            if (function_exists('schedule')) {
                schedule($this->schedule);
            }
        }
    }
}
