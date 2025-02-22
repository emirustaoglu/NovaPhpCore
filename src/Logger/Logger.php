<?php

namespace NovaCore\Logger;

class Logger implements LoggerInterface
{
    protected string $logPath;
    protected array $logLevels;
    protected string $logFilenamePattern;
    protected int $maxLogSize;

    public function __construct(array $config = [])
    {
        // Config dışarıdan gelmeli, varsayılanlar sağlanmalı
        $this->logPath = rtrim($config['log_directory'] ?? __DIR__ . '/logs', '/') . '/';
        $this->logLevels = $config['log_levels'] ?? ['error', 'warning', 'info', 'debug'];
        $this->logFilenamePattern = $config['log_filename_pattern'] ?? '{level}-{date}.log';
        $this->maxLogSize = $config['max_log_size'] ?? 5 * 1024 * 1024; // Varsayılan: 5MB

        // Log dizini oluştur
        if (!is_dir($this->logPath)) {
            mkdir($this->logPath, 0777, true);
        }
    }

    public function log(string $level, string $message, array $context = []): void
    {
        if (!in_array($level, $this->logLevels)) {
            return;
        }

        $date = date('Y-m-d H:i:s');
        $logFile = $this->getLogFilename($level);

        // Log mesajı oluştur
        $contextString = !empty($context) ? json_encode($context) : '';
        $logMessage = "[$date] [$level]: $message $contextString" . PHP_EOL;

        // Dosya boyutunu kontrol et ve yeni dosya oluştur
        if (file_exists($logFile) && filesize($logFile) >= $this->maxLogSize) {
            $logFile = $this->getLogFilename($level, true);
        }

        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }

    protected function getLogFilename(string $level, bool $rotated = false): string
    {
        $timestamp = $rotated ? '-' . date('Ymd-His') : '';
        return $this->logPath . str_replace(
                ['{level}', '{date}'],
                [$level, date('Y-m-d') . $timestamp],
                $this->logFilenamePattern
            );
    }

    // Log seviyelerine göre kolay erişim metotları
    public function info(string $message, array $context = []): void
    {
        $this->log('info', $message, $context);
    }

    public function warning(string $message, array $context = []): void
    {
        $this->log('warning', $message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        $this->log('error', $message, $context);
    }

    public function critical(string $message, array $context = []): void
    {
        $this->log('critical', $message, $context);
    }
}
