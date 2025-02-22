<?php

namespace NovaCore\Console;

use DirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;


class CLI
{
    protected array $commands = [];
    private $commandsDirectory;

    /**
     * Komutları dizinden otomatik olarak yükler.
     */
    public function __construct(string $commandsDirectory = __DIR__ . '/Commands')
    {
        $this->commandsDirectory = $commandsDirectory;
        $this->loadCommands($commandsDirectory);
    }

    /**
     * Komutları ilgili dizinden yükler.
     */
    private function loadCommands(string $commandsDirectory): void
    {
        $iterator = new DirectoryIterator($commandsDirectory);

        foreach ($iterator as $fileinfo) {
            if ($fileinfo->isFile() && $fileinfo->getExtension() === 'php') {
                $commandName = $fileinfo->getBasename('.php');
                $className = "NovaCore\\Console\\Commands\\$commandName";
                $this->commands[strtolower($commandName)] = $className;
            }
        }
    }

    /**
     * Komut satırını çalıştırır ve ilgili komutu uygular.
     */
    public function run(): void
    {
        $command = $this->getCommand();

        // -list komutunu kontrol et
        if ($command === '-list') {
            $this->listCommands();
        } elseif (strpos($command, 'make:') === 0) {
            $this->handleMakeCommand($command);
        } else {
            $this->handleCommand($command);
        }
    }

    /**
     * Komutları dinamik olarak listeleyen fonksiyon.
     */
    private function listCommands(): void
    {
        echo "Kullanılabilir komutlar:\n";

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->commandsDirectory, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $fileinfo) {
            if ($fileinfo->isFile() && $fileinfo->getExtension() === 'php') {
                // Komutun adını belirle
                $relativePath = str_replace($this->commandsDirectory . DIRECTORY_SEPARATOR, '', $fileinfo->getPathname());
                $commandName = str_replace(['.php', DIRECTORY_SEPARATOR], ['', ':'], $relativePath);

                // Sınıf adını oluştur
                $className = "NovaCore\\Console\\Commands\\" . str_replace(DIRECTORY_SEPARATOR, '\\', $relativePath);
                $className = str_replace('.php', '', $className);

                // Eğer sınıf tanımlıysa ve getDescription metodu varsa, açıklamayı al
                if (class_exists($className) && method_exists($className, 'getDescription')) {
                    $description = $className::getDescription();
                    echo " - $commandName => $description\n";
                }
            }
        }
    }

    private function handleCommand(string $command): void
    {
        // Komut sınıfını kontrol et
        $commandClass = "NovaCore\\Console\\Commands\\$command";

        if (class_exists($commandClass)) {
            $this->executeCommand($commandClass);
        } else {
            echo "Geçersiz komut: $command\n";
        }
    }

    /**
     * make:xxx komutlarını işle
     */
    private function handleMakeCommand(string $command): void
    {
        // 'make:' komutlarını ayır
        $parts = explode(':', $command);

        if (count($parts) < 2) {
            echo "Geçersiz komut formatı. Geçerli komut listesi php nova list komutunu kullanınız.";
            exit(1);
        }

        $action = $parts[1]; // 'migration', 'seed', 'view' gibi
        $className = ucfirst($action); // Migration -> MigrationCommand

        // Komut sınıfını kontrol et
        $commandClass = "NovaCore\\Console\\Commands\\Make\\$className";

        if (class_exists($commandClass)) {
            $this->executeCommand($commandClass);
        } else {
            echo "Geçersiz 'make' komutu: $command\n";
            exit(1);
        }
    }

    /**
     * Komut satırındaki komut parametresini alır.
     */
    private function getCommand(): string
    {
        global $argv;
        return isset($argv[1]) ? strtolower($argv[1]) : '';
    }

    /**
     * Komutu çalıştırır.
     */
    private function executeCommand(string $commandClass): void
    {
        $command = new $commandClass();
        $command->handle();
    }
}
