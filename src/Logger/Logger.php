<?php

namespace NovaCore\Logger;

use NovaCore\Config\ConfigLoader;

class Logger
{
    private string $channel;
    private string $logPath;
    private array $logLevels;
    private string $logFilenamePattern;
    private int $maxLogSize;

    public function __construct(string $channel = null)
    {
        $this->channel = $channel ?? ConfigLoader::getInstance()->get('logging.default');
        $config = ConfigLoader::getInstance()->get("logging.channels.{$this->channel}");

        if (empty($config)) {
            throw new \InvalidArgumentException("Logging channel '{$this->channel}' not configured");
        }

        $this->logPath = rtrim($config['log_directory'] ?? sys_get_temp_dir() . '/nova_logs', '/') . '/';
        $this->logLevels = $config['log_levels'] ?? ['error', 'warning', 'info', 'debug'];
        $this->logFilenamePattern = $config['log_filename_pattern'] ?? '{level}-{date}.log';
        $this->maxLogSize = $config['max_log_size'] ?? 5 * 1024 * 1024; // Default: 5MB

        if (!is_dir($this->logPath)) {
            mkdir($this->logPath, 0777, true);
        }
    }

    public function log(string $level, string $message, array $context = []): void
    {
        if (!in_array($level, $this->logLevels)) {
            return;
        }

        $filename = $this->getLogFilename($level);
        $logEntry = $this->formatLogEntry($level, $message, $context);

        if (file_exists($filename) && filesize($filename) > $this->maxLogSize) {
            $this->rotateLogFile($filename);
        }

        file_put_contents($filename, $logEntry . PHP_EOL, FILE_APPEND | LOCK_EX);
    }

    private function getLogFilename(string $level): string
    {
        $filename = str_replace(
            ['{level}', '{date}'],
            [$level, date('Y-m-d')],
            $this->logFilenamePattern
        );

        return $this->logPath . $filename;
    }

    private function formatLogEntry(string $level, string $message, array $context = []): string
    {
        $timestamp = date('Y-m-d H:i:s');
        $contextStr = empty($context) ? '' : ' ' . json_encode($context);
        
        return sprintf(
            '[%s] %s: %s%s',
            $timestamp,
            strtoupper($level),
            $message,
            $contextStr
        );
    }

    private function rotateLogFile(string $filename): void
    {
        $info = pathinfo($filename);
        $rotated = sprintf(
            '%s/%s-%s.%s',
            $info['dirname'],
            $info['filename'],
            date('Y-m-d-His'),
            $info['extension']
        );
        
        rename($filename, $rotated);
    }

    public function emergency(string $message, array $context = []): void
    {
        $this->log('emergency', $message, $context);
    }

    public function alert(string $message, array $context = []): void
    {
        $this->log('alert', $message, $context);
    }

    public function critical(string $message, array $context = []): void
    {
        $this->log('critical', $message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        $this->log('error', $message, $context);
    }

    public function warning(string $message, array $context = []): void
    {
        $this->log('warning', $message, $context);
    }

    public function notice(string $message, array $context = []): void
    {
        $this->log('notice', $message, $context);
    }

    public function info(string $message, array $context = []): void
    {
        $this->log('info', $message, $context);
    }

    public function debug(string $message, array $context = []): void
    {
        $this->log('debug', $message, $context);
    }
}
