<?php

namespace NovaCore\Console\Scheduling;

use DateTime;

class Event
{
    protected $command;
    protected array $arguments = [];
    protected bool $isShellCommand = false;
    protected array $filters = [];
    protected array $rejects = [];
    protected ?string $cron = null;
    protected ?string $timezone = null;
    protected ?string $user = null;
    protected array $environments = [];
    protected bool $evenInMaintenanceMode = false;
    protected bool $withoutOverlapping = false;
    protected ?int $expiresAt = null;
    protected array $beforeCallbacks = [];
    protected array $afterCallbacks = [];
    protected array $onSuccessCallbacks = [];
    protected array $onFailureCallbacks = [];

    public function __construct($command, array $arguments = [])
    {
        $this->command = $command;
        $this->arguments = $arguments;
    }

    public function cron(string $expression): self
    {
        $this->cron = $expression;
        return $this;
    }

    public function daily(): self
    {
        return $this->cron('0 0 * * *');
    }

    public function hourly(): self
    {
        return $this->cron('0 * * * *');
    }

    public function everyMinute(): self
    {
        return $this->cron('* * * * *');
    }

    public function everyFiveMinutes(): self
    {
        return $this->cron('*/5 * * * *');
    }

    public function everyFifteenMinutes(): self
    {
        return $this->cron('*/15 * * * *');
    }

    public function everyThirtyMinutes(): self
    {
        return $this->cron('*/30 * * * *');
    }

    public function weekly(): self
    {
        return $this->cron('0 0 * * 0');
    }

    public function monthly(): self
    {
        return $this->cron('0 0 1 * *');
    }

    public function timezone(string $timezone): self
    {
        $this->timezone = $timezone;
        return $this;
    }

    public function environments(array $environments): self
    {
        $this->environments = $environments;
        return $this;
    }

    public function evenInMaintenanceMode(): self
    {
        $this->evenInMaintenanceMode = true;
        return $this;
    }

    public function withoutOverlapping(int $expiresAt = 1440): self
    {
        $this->withoutOverlapping = true;
        $this->expiresAt = $expiresAt;
        return $this;
    }

    public function before(callable $callback): self
    {
        $this->beforeCallbacks[] = $callback;
        return $this;
    }

    public function after(callable $callback): self
    {
        $this->afterCallbacks[] = $callback;
        return $this;
    }

    public function onSuccess(callable $callback): self
    {
        $this->onSuccessCallbacks[] = $callback;
        return $this;
    }

    public function onFailure(callable $callback): self
    {
        $this->onFailureCallbacks[] = $callback;
        return $this;
    }

    public function when(callable $callback): self
    {
        $this->filters[] = $callback;
        return $this;
    }

    public function skip(callable $callback): self
    {
        $this->rejects[] = $callback;
        return $this;
    }

    public function isDue(): bool
    {
        if (!$this->passesFilters()) {
            return false;
        }

        return $this->expressionPasses();
    }

    public function run()
    {
        if ($this->withoutOverlapping && !$this->acquireLock()) {
            return;
        }

        try {
            $this->runCallbacks($this->beforeCallbacks);
            
            if (is_callable($this->command)) {
                $response = call_user_func_array($this->command, $this->arguments);
            } elseif ($this->isShellCommand) {
                $response = $this->runShellCommand();
            } else {
                $response = $this->runCommandInConsole();
            }

            $this->runCallbacks($this->afterCallbacks);

            if ($response !== false) {
                $this->runCallbacks($this->onSuccessCallbacks);
            } else {
                $this->runCallbacks($this->onFailureCallbacks);
            }
        } finally {
            if ($this->withoutOverlapping) {
                $this->releaseLock();
            }
        }
    }

    public function isShellCommand(): self
    {
        $this->isShellCommand = true;
        return $this;
    }

    protected function passesFilters(): bool
    {
        foreach ($this->filters as $callback) {
            if (!$callback()) {
                return false;
            }
        }

        foreach ($this->rejects as $callback) {
            if ($callback()) {
                return false;
            }
        }

        return true;
    }

    protected function expressionPasses(): bool
    {
        if (!$this->cron) {
            return false;
        }

        $date = new DateTime;
        if ($this->timezone) {
            $date->setTimezone(new \DateTimeZone($this->timezone));
        }

        return $this->getCronExpression()->isDue($date);
    }

    protected function getCronExpression()
    {
        return new \Cron\CronExpression($this->cron);
    }

    protected function acquireLock(): bool
    {
        // Lock implementation
        return true;
    }

    protected function releaseLock(): void
    {
        // Lock release implementation
    }

    protected function runShellCommand()
    {
        return shell_exec($this->command);
    }

    protected function runCommandInConsole()
    {
        // Command execution in console implementation
        return true;
    }

    protected function runCallbacks(array $callbacks): void
    {
        foreach ($callbacks as $callback) {
            $callback();
        }
    }
}
