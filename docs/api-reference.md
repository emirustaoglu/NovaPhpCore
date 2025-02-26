# NovaPHP Framework API Referansı

## Core Namespace

### NovaCore\Http

#### Controller
```php
abstract class Controller
{
    // Controller temel sınıfı
    public function view(string $view, array $data = []): View
    public function json(array $data, int $status = 200): JsonResponse
    public function redirect(string $url, int $status = 302): RedirectResponse
}
```

#### Request
```php
class Request
{
    // HTTP istek işleme
    public function all(): array
    public function input(string $key, $default = null): mixed
    public function has(string $key): bool
    public function file(string $key): ?UploadedFile
    public function method(): string
    public function isAjax(): bool
    public function ip(): string
    public function header(string $key, $default = null): mixed
}
```

### NovaCore\Database

#### Model
```php
abstract class Model
{
    // Temel model özellikleri
    protected string $table;
    protected array $fillable = [];
    protected array $hidden = [];
    protected array $casts = [];
    
    // CRUD işlemleri
    public static function create(array $attributes): static
    public function update(array $attributes): bool
    public function delete(): bool
    public static function find($id): ?static
    
    // İlişki metodları
    public function hasOne(string $related, string $foreignKey = null): HasOne
    public function hasMany(string $related, string $foreignKey = null): HasMany
    public function belongsTo(string $related, string $foreignKey = null): BelongsTo
    public function belongsToMany(string $related, string $table = null): BelongsToMany
}
```

### NovaCore\Security

#### Security
```php
class Security
{
    // Güvenlik metodları
    public function validateToken(string $token): bool
    public function sanitize($data): mixed
    public function sanitizeSql($input): string
    public function checkRateLimit(string $key, int $maxAttempts, int $decayMinutes): bool
    public function verifyPassword(string $password, string $hash): bool
    public function hashPassword(string $password): string
    public function secureSession(): void
    public function validateFileUpload(array $file, array $allowedTypes, int $maxSize): bool
    public function checkBruteForce(string $key, int $maxAttempts, int $blockMinutes): bool
    public function validateJWT(string $token, string $secret): ?array
}
```

### NovaCore\Cache

#### Cache
```php
class Cache
{
    // Önbellekleme metodları
    public static function get(string $key, $default = null): mixed
    public static function set(string $key, $value, int $ttl = null): bool
    public static function has(string $key): bool
    public static function delete(string $key): bool
    public static function clear(): bool
    public static function remember(string $key, int $ttl, Closure $callback): mixed
}
```

### NovaCore\Router

#### Router
```php
class Router
{
    // Routing metodları
    public function get(string $uri, $action): Route
    public function post(string $uri, $action): Route
    public function put(string $uri, $action): Route
    public function delete(string $uri, $action): Route
    public function group(array $attributes, Closure $callback): void
    public function middleware(string|array $middleware): Route
    public function name(string $name): Route
}
```

### NovaCore\View

#### View
```php
class View
{
    // View işleme metodları
    public static function make(string $view, array $data = []): View
    public function with(string $key, $value): View
    public function render(): string
    public static function share(string $key, $value): void
    public static function composer(string $view, Closure $callback): void
}
```

### NovaCore\Mail

#### Mail
```php
class Mail
{
    // Mail gönderim metodları
    public static function to(string|array $address): MailBuilder
    public static function send(string $view, array $data, Closure $callback): void
    public function attach(string $file, array $options = []): self
    public function queue(string $view, array $data): void
}
```

### NovaCore\Console

#### Command
```php
abstract class Command
{
    // Konsol komut metodları
    protected function argument(string $key): mixed
    protected function option(string $key): mixed
    protected function ask(string $question): string
    protected function secret(string $question): string
    protected function confirm(string $question): bool
    protected function info(string $message): void
    protected function error(string $message): void
}
```

## Helpers

```php
// View helpers
function view(string $view, array $data = []): View
function asset(string $path): string

// URL helpers
function url(string $path = null): string
function route(string $name, array $parameters = []): string
function redirect(string $to): RedirectResponse

// String helpers
function str_slug(string $string): string
function str_random(int $length = 16): string

// Array helpers
function array_get(array $array, string $key, $default = null): mixed
function array_set(array &$array, string $key, $value): array

// Security helpers
function csrf_token(): string
function csrf_field(): string
function auth(): ?Authenticatable
```
