# NovaPHP Framework Core

[![Latest Version on Packagist](https://img.shields.io/packagist/v/novaphp/core.svg?style=flat-square)](https://packagist.org/packages/novaphp/core)
[![Total Downloads](https://img.shields.io/packagist/dt/novaphp/core.svg?style=flat-square)](https://packagist.org/packages/novaphp/core)
[![License](https://img.shields.io/packagist/l/novaphp/core.svg?style=flat-square)](https://packagist.org/packages/novaphp/core)

NovaPHP, modern PHP uygulamaları geliştirmek için tasarlanmış güçlü ve esnek bir PHP framework'üdür. Yüksek performans, güvenlik ve kullanım kolaylığı göz önünde bulundurularak geliştirilmiştir.

## Özellikler

- Modern PHP 8.3+ desteği
- MVC mimari yapısı
- Güçlü routing sistemi
- Blade template motoru
- ORM veritabanı yönetimi
- Redis önbellekleme desteği
- Kapsamlı güvenlik önlemleri
- Dosya yükleme sistemi
- Mail gönderim desteği
- CLI komut desteği
- API geliştirme araçları

## Gereksinimler

- PHP >= 8.3
- PDO PHP Extension
- OpenSSL PHP Extension
- Mbstring PHP Extension
- Redis (önbellekleme için)
- Composer

## Kurulum

1. Composer ile projeyi oluşturun:
```bash
composer create-project novaphp/core proje-adi
```

2. .env dosyasını oluşturun:
```bash
cp .env.example .env
```

3. Uygulama anahtarını oluşturun:
```bash
php nova key:generate
```

4. Veritabanı ayarlarını yapılandırın:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=veritabani_adi
DB_USERNAME=kullanici_adi
DB_PASSWORD=sifre
```

## Temel Kullanım

### Routing

```php
// routes/web.php
$router->get('/', 'HomeController@index');

// Parametreli route
$router->get('/kullanici/{id}', 'UserController@show');

// Route grupları
$router->group(['prefix' => 'admin', 'middleware' => 'auth'], function(Router $router) {
    $router->get('dashboard', 'Admin\DashboardController@index');
});
```

### Controller

```php
namespace App\Controllers;

use NovaCore\Http\Controller;

class HomeController extends Controller
{
    public function index()
    {
        return view('home', ['title' => 'Ana Sayfa']);
    }
}
```

### Model

```php
namespace App\Models;

use NovaCore\Database\Model;

class User extends Model
{
    protected $table = 'users';
    protected $fillable = ['name', 'email', 'password'];
    
    // İlişkiler
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
```

### View

```php
<!-- resources/views/home.blade.php -->
@extends('layouts.app')

@section('content')
    <h1>{{ $title }}</h1>
    <p>Hoş geldiniz!</p>
@endsection
```

## Güvenlik

Framework, aşağıdaki güvenlik özelliklerini içerir:

- CSRF koruması
- XSS koruması
- SQL Injection koruması
- Session güvenliği
- Rate limiting
- Brute force koruması
- Güvenli dosya upload
- JWT desteği

```php
// Güvenlik örneği
use NovaCore\Security\Security;

class AuthController extends Controller
{
    private Security $security;
    
    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    
    public function login(array $input)
    {
        // Brute force kontrolü
        if (!$this->security->checkBruteForce('login', 5, 30)) {
            throw new \Exception('Çok fazla deneme yapıldı');
        }
        
        // Şifre doğrulama
        if (!$this->security->verifyPassword($input['password'], $hash)) {
            throw new \Exception('Geçersiz kimlik bilgileri');
        }
    }
}
```

## Cache Kullanımı

```php
use NovaCore\Cache\Cache;

// Veri önbellekleme
Cache::set('key', 'value', 3600); // 1 saat

// Önbellekten veri alma
$value = Cache::get('key', 'varsayılan');

// Önbellek silme
Cache::delete('key');
```

## Mail Gönderimi

```php
use NovaCore\Mail\Mail;

Mail::to('user@example.com')
    ->subject('Hoş Geldiniz')
    ->view('emails.welcome', ['user' => $user])
    ->send();
```

## CLI Komutları

```bash
# Migration oluşturma
php nova make:migration create_users_table

# Migration çalıştırma
php nova migrate

# Controller oluşturma
php nova make:controller UserController

# Model oluşturma
php nova make:model User
```

## Örnekler

Daha fazla örnek kod için `examples/framework` dizinini inceleyebilirsiniz. Bu dizinde:

- Temel CRUD işlemleri
- Authentication sistemi
- Admin panel
- API endpoint'leri
- Form işlemleri
- Dosya upload
- ve daha fazlası

## Katkıda Bulunma

1. Fork edin
2. Feature branch oluşturun (`git checkout -b feature/amazing-feature`)
3. Değişikliklerinizi commit edin (`git commit -m 'feat: Add amazing feature'`)
4. Branch'inizi push edin (`git push origin feature/amazing-feature`)
5. Pull Request oluşturun

## Lisans

Bu proje MIT lisansı altında lisanslanmıştır. Daha fazla bilgi için `LICENSE` dosyasını inceleyebilirsiniz.

## Faydalı Linkler

- [Detaylı Dokümantasyon](docs/README.md)
- [API Referansı](docs/api-reference.md)
- [Güvenlik Politikası](SECURITY.md)
- [Katkıda Bulunma Rehberi](CONTRIBUTING.md)

[![Latest Version on Packagist](https://img.shields.io/packagist/v/novaphp/core.svg?style=flat-square)](https://packagist.org/packages/novaphp/core)
[![Total Downloads](https://img.shields.io/packagist/dt/novaphp/core.svg?style=flat-square)](https://packagist.org/packages/novaphp/core)
[![License](https://img.shields.io/packagist/l/novaphp/core.svg?style=flat-square)](https://packagist.org/packages/novaphp/core)

NovaPHP, modern PHP uygulamaları için geliştirilmiş, güvenlik odaklı bir PHP framework'üdür. Yüksek performans, kolay kullanım ve güçlü güvenlik özellikleri ile öne çıkar.

## Özellikler

- 🚀 Yüksek Performans
- 🛡️ Gelişmiş Güvenlik Özellikleri
- 📦 Kolay Kurulum ve Kullanım
- 🎯 Modern PHP Pratikleri (PHP 8.3+)
- 🔧 Esnek ve Genişletilebilir Yapı
- 📝 Kapsamlı Dokümantasyon

## Güvenlik Özellikleri

- XSS (Cross-Site Scripting) Koruması
- SQL Injection Koruması
- CSRF Token Koruması
- Rate Limiting
- Güvenli Dosya Upload İşlemleri
- JWT Desteği
- Brute Force Koruması

## Kurulum

Composer kullanarak NovaPHP'yi projenize ekleyebilirsiniz:

```bash
composer require novaphp/core
```

## Hızlı Başlangıç

1. Yeni bir proje oluşturun:

```php
<?php

require 'vendor/autoload.php';

use NovaCore\Http\Application;

$app = new Application(__DIR__);
```

2. Route tanımlayın:

```php
use NovaCore\Router\Router;

Router::get('/', function() {
    return 'Merhaba NovaPHP!';
});
```

3. Controller kullanımı:

```php
use NovaCore\Http\Controller;

class UserController extends Controller
{
    public function index()
    {
        return view('users.index', ['users' => User::all()]);
    }
}
```

## Veritabanı Kullanımı

```php
use NovaCore\Database\DB;

// Sorgu Oluşturma
$users = DB::table('users')
    ->where('active', true)
    ->get();

// Tek Kayıt
$user = DB::table('users')->find(1);

// Kayıt Ekleme
DB::table('users')->insert([
    'name' => 'John Doe',
    'email' => 'john@example.com'
]);
```

## Security Sınıfı Kullanımı

```php
use NovaCore\Security\Security;

$security = new Security();

// XSS Koruması
$cleanData = $security->sanitize($userInput);

// Token Doğrulama
if ($security->validateToken($token)) {
    // İşlem güvenli
}
```

## Katkıda Bulunma

1. Bu repository'yi fork edin
2. Feature branch'inizi oluşturun (`git checkout -b feature/amazing-feature`)
3. Değişikliklerinizi commit edin (`git commit -m 'feat: Add amazing feature'`)
4. Branch'inizi push edin (`git push origin feature/amazing-feature`)
5. Pull Request oluşturun

## Lisans

Bu proje MIT lisansı altında lisanslanmıştır. Detaylar için [LICENSE](LICENSE) dosyasına bakın.

## İletişim

Yusuf Emir USTAOĞLU - [@emiru893](https://twitter.com/emiru893) - emiru893@gmail.com

Proje Linki: [https://github.com/emirustaoglu/NovaPhpCore](https://github.com/emirustaoglu/NovaPhpCore)
