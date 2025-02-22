<?php

namespace NovaCore\Database;

class SeedsManager
{
    private $seedsPath;
    private $seeds;

    public function __construct($seedsPath, $settings)
    {
        $this->seedsPath = $seedsPath;
        if (!file_exists($this->seedsPath)) {
            die('Belirtilen seeds dosya yolu bulunamadı.');
        }
        $this->seeds = new Seeds($settings['tableName'], $settings['host'], $settings['dbname'], $settings['username'], $settings['password']);
    }

    public function runSeeds($direction = 'up')
    {
        $files = scandir($this->seedsPath);
        if (count($files) < 3) {
            die('İşlem yapılacak seeds dosyası bulunamadı.');
        }
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                $this->seeds->apply($this->seedsPath . DIRECTORY_SEPARATOR . $file, $direction);
            }
        }
    }

    public function seedsUp()
    {
        $this->runSeeds('up');
    }

    public function seedsDown()
    {
        $this->runSeeds('down');
    }
}