<?php

namespace NovaCore\Cache;

class Cache
{
    protected string $cachePath;
    protected int $expirationTime;

    /**
     * Cache constructor.
     *
     * @param string $cachePath Cache dosyalarının yolu
     * @param int $expirationTime Cache'in süre sonlanma süresi (saniye cinsinden)
     */
    public function __construct(string $cachePath, int $expirationTime = 3600)
    {
        $this->cachePath = rtrim($cachePath, '/');
        $this->expirationTime = $expirationTime;  // Cache'in geçerlilik süresi (default: 1 saat)
    }

    /**
     * Cache'e veri ekler
     *
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public function set(string $key, $value): bool
    {
        $filePath = $this->getCacheFilePath($key);
        $data = [
            'value' => $value,
            'expires_at' => time() + $this->expirationTime  // Geçerlilik süresi
        ];

        // JSON formatında cache verisini dosyaya kaydet
        return file_put_contents($filePath, json_encode($data)) !== false;
    }

    /**
     * Cache'den veri alır
     *
     * @param string $key
     * @return mixed|null
     */
    public function get(string $key)
    {
        $filePath = $this->getCacheFilePath($key);

        // Cache dosyası yoksa veya cache süresi dolmuşsa null döndür
        if (!file_exists($filePath)) {
            return null;
        }

        $data = json_decode(file_get_contents($filePath), true);

        // Cache süresi dolmuşsa veriyi sil ve null döndür
        if (time() > $data['expires_at']) {
            unlink($filePath); // Cache süresi bitmişse dosyayı sil
            return null;
        }

        return $data['value'];
    }

    /**
     * Cache dosyasını siler
     *
     * @param string $key
     * @return bool
     */
    public function delete(string $key): bool
    {
        $filePath = $this->getCacheFilePath($key);

        if (file_exists($filePath)) {
            return unlink($filePath); // Dosyayı sil
        }

        return false;
    }

    /**
     * Cache dosyasının yolunu oluşturur
     *
     * @param string $key
     * @return string
     */
    protected function getCacheFilePath(string $key): string
    {
        // Anahtarın hash'ini alarak dosya ismi oluştur
        $hashedKey = md5($key);
        return $this->cachePath . '/' . $hashedKey . '.cache';
    }

    /**
     * Cache dizinini oluşturur (eğer yoksa)
     */
    protected function createCacheDirectory(): void
    {
        if (!is_dir($this->cachePath)) {
            mkdir($this->cachePath, 0777, true);
        }
    }
}
