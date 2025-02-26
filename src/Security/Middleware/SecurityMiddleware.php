<?php

namespace NovaCore\Security\Middleware;

use NovaCore\Security\Security;
use NovaCore\Config\ConfigLoader;

class SecurityMiddleware
{
    private Security $security;
    private array $config;

    public function __construct(Security $security)
    {
        $this->security = $security;
        $this->config = ConfigLoader::getInstance()->get('security');
    }

    public function handle($request, \Closure $next)
    {
        // Güvenli session başlatma
        $this->security->secureSession();

        // Güvenlik başlıkları ayarlama
        $this->security->setSecurityHeaders();

        // CSRF koruma kontrolü
        if ($this->config['csrf']['enabled'] ?? true) {
            if ($this->isPostRequest()) {
                $token = $_POST['_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
                if (!$this->security->validateToken($token)) {
                    throw new \Exception('CSRF token validation failed');
                }
            }
        }

        // XSS koruma
        $_GET = $this->security->sanitize($_GET);
        $_POST = $this->security->sanitize($_POST);
        $_COOKIE = $this->security->sanitize($_COOKIE);

        // SQL Injection koruması
        $_GET = $this->security->sanitizeSql($_GET);
        $_POST = $this->security->sanitizeSql($_POST);

        // Rate limiting
        if ($this->config['rate_limit']['enabled'] ?? true) {
            $ip = $this->getClientIp();
            $route = $_SERVER['REQUEST_URI'] ?? '/';
            $rateLimitKey = "route:{$route}:ip:{$ip}";
            
            try {
                $maxAttempts = $this->config['rate_limit']['default_max_attempts'] ?? 60;
                $decayMinutes = $this->config['rate_limit']['default_decay_minutes'] ?? 1;

                if (!$this->security->checkRateLimit($rateLimitKey, $maxAttempts, $decayMinutes, $ip)) {
                    $remaining = $this->security->getRateLimitRemaining($rateLimitKey, $maxAttempts);
                    header('X-RateLimit-Remaining: ' . $remaining);
                    header('X-RateLimit-Limit: ' . $maxAttempts);
                    header('Retry-After: ' . ($decayMinutes * 60));
                    http_response_code(429);
                    exit('Too Many Requests');
                }
            } catch (\Exception $e) {
                // Rate limit hatası durumunda loglama
                error_log("Rate limit error: " . $e->getMessage());
            }
        }

        return $next($request);
    }

    private function isPostRequest(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    private function getClientIp(): string
    {
        $ipSources = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];

        foreach ($ipSources as $key) {
            if (isset($_SERVER[$key])) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, 
                        FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                        return $ip;
                    }
                }
            }
        }

        return '0.0.0.0';
    }
}
