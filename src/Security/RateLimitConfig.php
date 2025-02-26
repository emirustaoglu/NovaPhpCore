<?php

namespace NovaCore\Security;

class RateLimitConfig
{
    private array $groups = [];
    private array $whitelist = [];
    private array $blacklist = [];
    
    public function addGroup(string $name, int $maxAttempts, int $decayMinutes): self
    {
        $this->groups[$name] = [
            'maxAttempts' => $maxAttempts,
            'decayMinutes' => $decayMinutes
        ];
        return $this;
    }
    
    public function addToWhitelist(string $ip): self
    {
        $this->whitelist[] = $ip;
        return $this;
    }
    
    public function addToBlacklist(string $ip): self
    {
        $this->blacklist[] = $ip;
        return $this;
    }
    
    public function isWhitelisted(string $ip): bool
    {
        return in_array($ip, $this->whitelist) || $this->matchesIpPattern($ip, $this->whitelist);
    }
    
    public function isBlacklisted(string $ip): bool
    {
        return in_array($ip, $this->blacklist) || $this->matchesIpPattern($ip, $this->blacklist);
    }
    
    public function getGroup(string $name): ?array
    {
        return $this->groups[$name] ?? null;
    }
    
    private function matchesIpPattern(string $ip, array $patterns): bool
    {
        foreach ($patterns as $pattern) {
            if (strpos($pattern, '*') !== false) {
                $pattern = str_replace('*', '.*', $pattern);
                if (preg_match('/^' . $pattern . '$/', $ip)) {
                    return true;
                }
            }
        }
        return false;
    }
}
