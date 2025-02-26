<?php

namespace NovaCore\Security;

use NovaCore\Config\ConfigLoader;

class Security
{
    private string $token;
    private RateLimit $rateLimit;
    private array $config;

    public function __construct(RateLimit $rateLimit = null)
    {
        $this->config = ConfigLoader::getInstance()->get('security');
        $this->token = $this->generateToken();
        $this->rateLimit = $rateLimit ?? new RateLimit(
            new \NovaCore\Cache\RedisCache(
                ConfigLoader::getInstance()->get('cache.stores.redis')
            )
        );
    }

    /**
     * CSRF token oluşturur
     */
    private function generateToken(): string
    {
        $length = $this->config['csrf']['token_length'] ?? 32;
        return bin2hex(random_bytes($length));
    }

    /**
     * CSRF token'ı doğrular
     */
    public function validateToken(string $token): bool
    {
        if (!($this->config['csrf']['enabled'] ?? true)) {
            return true;
        }
        return hash_equals($this->token, $token);
    }

    /**
     * XSS temizleme
     */
    public function sanitize($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->sanitize($value);
            }
            return $data;
        }
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Rate limiting kontrolü
     */
    public function checkRateLimit(string $key, int $maxAttempts = null, int $decayMinutes = null, string $ip = null): bool
    {
        if (!($this->config['rate_limit']['enabled'] ?? true)) {
            return true;
        }

        $maxAttempts = $maxAttempts ?? $this->config['rate_limit']['default_max_attempts'] ?? 60;
        $decayMinutes = $decayMinutes ?? $this->config['rate_limit']['default_decay_minutes'] ?? 1;

        return $this->rateLimit->attempt($key, $maxAttempts, $decayMinutes, $ip);
    }

    /**
     * Rate limit kalan deneme sayısını döndürür
     */
    public function getRateLimitRemaining(string $key, int $maxAttempts): int
    {
        return $this->rateLimit->remaining($key, $maxAttempts);
    }

    /**
     * Rate limit sıfırlama
     */
    public function resetRateLimit(string $key): void
    {
        $this->rateLimit->resetAttempts($key);
    }

    /**
     * Güvenlik başlıklarını ayarlar
     */
    public function setSecurityHeaders(): void
    {
        foreach ($this->config['headers'] ?? [] as $header => $value) {
            header("$header: $value");
        }
    }

    /**
     * Güvenli şifreleme
     */
    public function encrypt(string $data, string $key): string
    {
        $cipher = "aes-256-gcm";
        $ivlen = openssl_cipher_iv_length($cipher);
        $iv = openssl_random_pseudo_bytes($ivlen);
        $tag = "";
        
        $encrypted = openssl_encrypt($data, $cipher, $key, OPENSSL_RAW_DATA, $iv, $tag);
        return base64_encode($iv . $tag . $encrypted);
    }

    /**
     * Şifre çözme
     */
    public function decrypt(string $data, string $key): string
    {
        $cipher = "aes-256-gcm";
        $data = base64_decode($data);
        $ivlen = openssl_cipher_iv_length($cipher);
        $iv = substr($data, 0, $ivlen);
        $tag = substr($data, $ivlen, 16);
        $encrypted = substr($data, $ivlen + 16);
        
        return openssl_decrypt($encrypted, $cipher, $key, OPENSSL_RAW_DATA, $iv, $tag);
    }
}
