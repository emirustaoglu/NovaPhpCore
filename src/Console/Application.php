<?php

namespace NovaCore\Console;

class Application
{
    protected array $commands = [];
    protected array $defaultCommands = [
        Commands\Upload\InstallCommand::class,
        Commands\Upload\StatsCommand::class,
        Commands\Upload\CleanupCommand::class,
        Commands\Upload\VerifyCommand::class,
        Commands\Upload\SeedCommand::class
    ];

    public function __construct()
    {
        $this->registerDefaultCommands();
    }

    protected function registerDefaultCommands(): void
    {
        foreach ($this->defaultCommands as $command) {
            $this->add(new $command());
        }
    }

    public function add(Command $command): void
    {
        $this->commands[$command->getSignature()] = $command;
    }

    public function run(array $argv = []): int
    {
        try {
            $command = $argv[1] ?? 'list';

            if ($command === 'list') {
                return $this->listCommands();
            }

            if (!isset($this->commands[$command])) {
                throw new \RuntimeException("Komut bulunamadı: {$command}");
            }

            $args = array_slice($argv, 2);
            return $this->commands[$command]->execute($args);

        } catch (\Exception $e) {
            echo "Hata: " . $e->getMessage() . "\n";
            return 1;
        }
    }

    protected function listCommands(): int
    {
        echo "Kullanılabilir komutlar:\n\n";

        foreach ($this->commands as $signature => $command) {
            echo sprintf(
                "  %-30s %s\n",
                $signature,
                $command->getDescription()
            );
        }

        return 0;
    }
}
