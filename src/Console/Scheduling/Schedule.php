<?php

namespace NovaCore\Console\Scheduling;

class Schedule
{
    protected array $events = [];

    public function command(string $command, array $arguments = []): Event
    {
        $event = new Event($command, $arguments);
        $this->events[] = $event;
        return $event;
    }

    public function exec(string $command): Event
    {
        $event = new Event($command);
        $event->isShellCommand();
        $this->events[] = $event;
        return $event;
    }

    public function call(callable $callback, array $arguments = []): Event
    {
        $event = new Event($callback, $arguments);
        $this->events[] = $event;
        return $event;
    }

    public function dueEvents(): array
    {
        return array_filter($this->events, function (Event $event) {
            return $event->isDue();
        });
    }

    public function getEvents(): array
    {
        return $this->events;
    }
}
