<?php

namespace NovaPhp\Core\Console;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class CLI
{
    protected array $commands = [];
    protected array $commandGroups = [];
    protected string $commandsDirectory;
    protected string $frameworkPath;

    public function __construct(?string $frameworkPath = null)
    {
        // Core komutlarını yükle
        $coreCommands = __DIR__ . DIRECTORY_SEPARATOR . 'Commands' . DIRECTORY_SEPARATOR;
        if (is_dir($coreCommands)) {
            $this->loadCommands($coreCommands);
        }
    }

    private function loadCommands(string $directory): void
    {
        if (!is_dir($directory)) {
            return;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $fileinfo) {
            if ($fileinfo->isFile() && $fileinfo->getExtension() === 'php') {
                // Normalize path
                $normalizedDirectory = realpath($directory);
                $normalizedPath = realpath($fileinfo->getPathname());

                // Get relative path
                $relativePath = substr($normalizedPath, strlen($normalizedDirectory) + 1);

                // Normalize namespace
                $relativePath = str_replace(['.php', DIRECTORY_SEPARATOR], ['', '\\'], $relativePath);
                $className = "NovaPhp\\Core\\Console\\Commands\\" . $relativePath;

                // Eğer autoload kullanılmıyorsa dosyayı manuel yükle
                if (!class_exists($className)) {
                    require_once $fileinfo->getPathname();
                }
                // Sınıf mevcutsa yükle
                if (class_exists($className)) {
                    $command = new $className();
                    $this->addCommand($command);
                }
            }
        }
    }

    private function addCommand($command): void
    {
        $signature = $command->getSignature();
        $this->commands[$signature] = $command;

        $parts = explode(':', $signature);
        if (count($parts) > 1) {
            $group = $parts[0];
            if (!isset($this->commandGroups[$group])) {
                $this->commandGroups[$group] = [];
            }
            $this->commandGroups[$group][] = $signature;
        }
    }

    /**
     * Komut satırını çalıştırır
     */
    public function run(): void
    {

        $command = $_SERVER['argv'][1] ?? '-list';

        $args = array_slice($_SERVER['argv'], 2);

        if ($command === '-list') {
            $this->listCommands();
            return;
        }

        // make: ve route: komutları için özel kontrol
        if (strpos($command, 'make:') === 0 || strpos($command, 'route:') === 0) {
            $parts = explode(':', $command);
            $type = $parts[0];
            $action = $parts[1] ?? '';

            if (empty($action)) {
                $this->error("Geçersiz komut formatı");
                return;
            }

            $className = ucfirst($action);
            $namespace = $type === 'make' ? 'Make' : 'Route';
            $commandClass = "NovaPhp\\Core\\Console\\Commands\\{$namespace}\\{$className}";

            if (class_exists($commandClass)) {
                $instance = new $commandClass();
                $instance->execute($args);
                return;
            }
        }

        // Diğer komutlar için normal akış
        if (isset($this->commands[$command])) {
            $this->commands[$command]->execute($args);
        } else {
            $this->error("Geçersiz komut: $command");
            $this->info("Kullanılabilir komutları görmek için -list kullanın");
        }
    }

    /**
     * Komutları gruplar halinde listeler
     */
    private function listCommands(): void
    {
        echo Helper::novaPhpAsci();
        echo "\033[1mKullanılabilir Komutlar:\033[0m\n\n";

        // Önce gruplanmış komutları göster
        foreach ($this->commandGroups as $group => $commands) {
            echo "\033[33m" . ucfirst($group) . " Komutları:\033[0m\n";
            foreach ($commands as $command) {
                $this->printCommand($command);
            }
            echo "\n";
        }

        // Gruplanmamış komutları göster
        $ungrouped = array_diff(
            array_keys($this->commands),
            array_merge(...array_values($this->commandGroups))
        );

        if (!empty($ungrouped)) {
            echo "\033[33mDiğer Komutlar:\033[0m\n";
            foreach ($ungrouped as $command) {
                $this->printCommand($command);
            }
        }
    }

    /**
     * Komut bilgilerini formatlar ve yazdırır
     */
    private function printCommand(string $command): void
    {
        $description = $this->commands[$command]->getDescription();
        printf("  \033[32m%-30s\033[0m %s\n", $command, $description);
    }

    /**
     * Hata mesajı yazdırır
     */
    private function error(string $message): void
    {
        echo "\033[31m" . $message . "\033[0m\n";
    }

    /**
     * Bilgi mesajı yazdırır
     */
    private function info(string $message): void
    {
        echo "\033[32m" . $message . "\033[0m\n";
    }
}
