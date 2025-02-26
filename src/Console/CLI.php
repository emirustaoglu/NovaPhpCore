<?php

namespace NovaCore\Console;

use DirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class CLI
{
    protected array $commands = [];
    protected array $commandGroups = [];
    protected string $commandsDirectory;
    protected string $frameworkPath;

    /**
     * CLI sınıfını başlatır
     */
    public function __construct(?string $frameworkPath = null)
    {
        // Framework yolunu belirle
        $this->frameworkPath = $frameworkPath ?? getcwd();
        
        // Önce framework'ün komutlarını yükle
        $frameworkCommands = $this->frameworkPath . '/app/Console/Commands';
        if (is_dir($frameworkCommands)) {
            $this->loadCommands($frameworkCommands);
        }

        // Sonra core komutlarını yükle
        $coreCommands = __DIR__ . '/Commands';
        if (is_dir($coreCommands)) {
            $this->loadCommands($coreCommands);
        }
    }

    /**
     * Komutları ilgili dizinden yükler
     */
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
                $relativePath = str_replace($directory . DIRECTORY_SEPARATOR, '', $fileinfo->getPathname());
                $namespace = str_replace(['.php', DIRECTORY_SEPARATOR], ['', '\\'], $relativePath);
                
                // Framework komutları için App namespace'i
                if (strpos($directory, 'app/Console/Commands') !== false) {
                    $className = "App\\Console\\Commands\\" . $namespace;
                } 
                // Core komutları için NovaCore namespace'i
                else {
                    $className = "NovaCore\\Console\\Commands\\" . $namespace;
                }

                if (class_exists($className)) {
                    $command = new $className();
                    $this->addCommand($command);
                }
            }
        }
    }

    /**
     * Komutu ekler ve gruplara ayırır
     */
    private function addCommand($command): void
    {
        $signature = $command->getSignature();
        $this->commands[$signature] = $command;

        // Grup bazlı organizasyon
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
            $commandClass = "NovaCore\\Console\\Commands\\{$namespace}\\{$className}";

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
