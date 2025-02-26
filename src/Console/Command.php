<?php

namespace NovaCore\Console;

abstract class Command
{
    protected string $signature;
    protected string $description;
    protected array $arguments = [];
    protected array $argumentDefinitions = [];

    public function getSignature(): string
    {
        return $this->signature;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function execute(array $args = []): int
    {
        try {
            $this->parseSignature();
            $this->parseArguments($args);
            $this->handle();
            return 0;
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            return 1;
        }
    }

    abstract public function handle(): void;

    protected function parseSignature(): void
    {
        preg_match_all('/{(\w+)}/', $this->signature, $matches);
        foreach ($matches[1] as $argument) {
            $this->argumentDefinitions[] = $argument;
        }
    }

    protected function parseArguments(array $args): void
    {
        // İsimli argümanları işle
        foreach ($args as $arg) {
            if (strpos($arg, '=') !== false) {
                [$key, $value] = explode('=', $arg, 2);
                $this->arguments[ltrim($key, '-')] = $value;
            }
        }

        // Pozisyonel argümanları işle
        $positionalArgs = array_filter($args, fn($arg) => strpos($arg, '=') === false);
        foreach ($this->argumentDefinitions as $index => $name) {
            if (isset($positionalArgs[$index])) {
                $this->arguments[$name] = $positionalArgs[$index];
            }
        }
    }

    protected function argument(string $key, $default = null)
    {
        return $this->arguments[$key] ?? $default;
    }

    protected function info(string $message): void
    {
        echo "\033[32m" . $message . "\033[0m\n";
    }

    protected function error(string $message): void
    {
        echo "\033[31m" . $message . "\033[0m\n";
    }

    protected function warn(string $message): void
    {
        echo "\033[33m" . $message . "\033[0m\n";
    }

    protected function table(array $headers, array $rows): void
    {
        // En uzun değerlere göre kolon genişliklerini hesapla
        $widths = [];
        foreach ($headers as $i => $header) {
            $widths[$i] = strlen($header);
            foreach ($rows as $row) {
                $widths[$i] = max($widths[$i], strlen($row[$i] ?? ''));
            }
        }

        // Başlıkları yazdır
        $this->printTableRow($headers, $widths);
        
        // Ayraç çizgisi
        $separator = '';
        foreach ($widths as $width) {
            $separator .= '+' . str_repeat('-', $width + 2);
        }
        echo $separator . "+\n";

        // Satırları yazdır
        foreach ($rows as $row) {
            $this->printTableRow($row, $widths);
        }
    }

    protected function printTableRow(array $row, array $widths): void
    {
        $line = '';
        foreach ($widths as $i => $width) {
            $value = $row[$i] ?? '';
            $line .= '| ' . str_pad($value, $width) . ' ';
        }
        echo $line . "|\n";
    }

    protected function confirm(string $question): bool
    {
        echo $question . " (evet/hayır) [hayır]: ";
        $answer = trim(fgets(STDIN));
        return strtolower($answer) === 'evet';
    }

    protected function ask(string $question, $default = null): string
    {
        $defaultText = $default ? " [{$default}]" : '';
        echo $question . $defaultText . ": ";
        $answer = trim(fgets(STDIN));
        return $answer ?: $default;
    }

    protected function choice(string $question, array $choices, $default = null): string
    {
        echo $question . "\n";
        foreach ($choices as $i => $choice) {
            echo "  " . ($i + 1) . ") {$choice}\n";
        }

        $defaultText = $default ? " [{$default}]" : '';
        echo "Seçiminiz{$defaultText}: ";
        $answer = trim(fgets(STDIN));

        if (!$answer && $default !== null) {
            return $choices[$default - 1];
        }

        $index = (int)$answer - 1;
        if (!isset($choices[$index])) {
            throw new \RuntimeException('Geçersiz seçim');
        }

        return $choices[$index];
    }
}
