<?php

namespace NovaCore\Config;

class Config
{
    protected array $config = [];
    protected string $configPath;

    /**
     * Config constructor.
     *
     * @param string $configPath Config dosyalarının yolu
     */
    public function __construct(string $configPath = __DIR__ . '/../../config')
    {
        $this->configPath = rtrim($configPath, '/'); // Yolu düzenle
        $this->loadConfigFiles(); // config dosyalarını yükler
    }

    /**
     * config dosyalarını yükler
     */
    public function loadConfigFiles(): void
    {
        // config dizinindeki tüm PHP dosyalarını al
        $configFiles = glob($this->configPath . '/*.php');

        foreach ($configFiles as $file) {
            $fileName = basename($file, '.php'); // dosya ismini al (örneğin database.php => database)
            $this->config[$fileName] = require $file; // dosyayı include et ve içeriğini $config dizisine aktar
        }
    }

    /**
     * dot notation ile konfigürasyon değerine erişim
     *
     * @param string $key
     * @return mixed|null
     */
    public function get(string $key)
    {
        $keys = explode('.', $key); // key'i noktalarla ayırarak bir dizi oluştur
        $value = $this->config;

        // dizi üzerinde gezerek değeri bulmaya çalış
        foreach ($keys as $keyPart) {
            if (isset($value[$keyPart])) {
                $value = $value[$keyPart]; // değeri güncelle
            } else {
                return null; // Anahtar bulunamazsa null döndür
            }
        }

        return $value;
    }
}
