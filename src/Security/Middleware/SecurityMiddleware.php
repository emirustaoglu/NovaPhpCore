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
        // CSRF koruma kontrolü
        if ($this->config['csrf']['enabled'] ?? true) {
            if ($this->isPostRequest()) {
                $token = $_POST['_token'] ?? '';
                if (!$this->security->validateToken($token)) {
                    throw new \Exception('CSRF token validation failed');
                }
            }
        }

        // XSS koruma
        $_GET = $this->security->sanitize($_GET);
        $_POST = $this->security->sanitize($_POST);
        $_COOKIE = $this->security->sanitize($_COOKIE);

        // Rate limiting
        if ($this->config['rate_limit']['enabled'] ?? true) {
            $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
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
                    throw new \Exception('Too many requests. Please try again later.');
                }
            } catch (\Exception $e) {
                if ($e->getMessage() === 'Rate limit lock could not be acquired') {
                    http_response_code(503);
                    throw new \Exception('Service temporarily unavailable. Please try again.');
                }
                throw $e;
            }
        }

        // Güvenlik headerları
        $this->security->setSecurityHeaders();

        return $next($request);
    }

    private function isPostRequest(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
}
