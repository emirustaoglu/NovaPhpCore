<?php

namespace NovaCore\Security;

use NovaCore\Config\ConfigLoader;

class Security
{
    private string $token;
    private RateLimit $rateLimit;
    private array $config;
    private array $securityHeaders = [
        'X-Content-Type-Options' => 'nosniff',
        'X-Frame-Options' => 'SAMEORIGIN',
        'X-XSS-Protection' => '1; mode=block',
        'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains',
        'Content-Security-Policy' => "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline';",
        'Referrer-Policy' => 'strict-origin-when-cross-origin',
        'Permissions-Policy' => 'geolocation=(), microphone=(), camera=()'
    ];

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
     * SQL Injection koruması için input temizleme
     */
    public function sanitizeSql($input): string
    {
        if (is_array($input)) {
            return array_map([$this, 'sanitizeSql'], $input);
        }
        
        if (!is_string($input)) {
            return $input;
        }

        // SQL injection karakterlerini temizle
        $search = array("\\", "\x00", "\n", "\r", "'", '"', "\x1a");
        $replace = array("\\\\", "\\0", "\\n", "\\r", "\\'", '\\"', "\\Z");
        return str_replace($search, $replace, $input);
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
     * Password hash kontrolü
     */
    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Güvenli password hash oluşturma
     */
    public function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_ARGON2ID, [
            'memory_cost' => 65536,
            'time_cost' => 4,
            'threads' => 3
        ]);
    }

    /**
     * Session güvenliği ayarları
     */
    public function secureSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.cookie_httponly', '1');
            ini_set('session.cookie_secure', '1');
            ini_set('session.cookie_samesite', 'Strict');
            ini_set('session.use_strict_mode', '1');
            ini_set('session.gc_maxlifetime', '3600');
            session_start();
        }
    }

    /**
     * Güvenli dosya upload kontrolü
     */
    public function validateFileUpload(array $file, array $allowedTypes = [], int $maxSize = 5242880): bool
    {
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return false;
        }

        // Dosya boyutu kontrolü
        if ($file['size'] > $maxSize) {
            return false;
        }

        // MIME type kontrolü
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!empty($allowedTypes) && !in_array($mimeType, $allowedTypes)) {
            return false;
        }

        return true;
    }

    /**
     * IP bazlı brute force koruması
     */
    public function checkBruteForce(string $key, int $maxAttempts = 5, int $blockMinutes = 30): bool
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $bruteForceKey = "brute_force:{$key}:ip:{$ip}";
        
        return $this->checkRateLimit($bruteForceKey, $maxAttempts, $blockMinutes, $ip);
    }

    /**
     * Güvenli random string üretme
     */
    public function generateSecureString(int $length = 32): string
    {
        return bin2hex(random_bytes($length));
    }

    /**
     * JWT token doğrulama
     */
    public function validateJWT(string $token, string $secret): ?array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }

        $header = json_decode(base64_decode($parts[0]), true);
        $payload = json_decode(base64_decode($parts[1]), true);
        $signature = $parts[2];

        $validSignature = hash_hmac('sha256', $parts[0] . '.' . $parts[1], $secret, true);
        $validSignature = base64_encode($validSignature);

        if ($signature !== $validSignature) {
            return null;
        }

        if (isset($payload['exp']) && $payload['exp'] < time()) {
            return null;
        }

        return $payload;
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
