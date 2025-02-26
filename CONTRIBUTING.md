# Katkıda Bulunma Rehberi

## 👋 Hoş Geldiniz!

NovaPHP Framework'e katkıda bulunmak istediğiniz için teşekkür ederiz! Bu rehber, projeye nasıl katkıda bulunabileceğinizi açıklar.

## 🚀 Başlarken

1. Projeyi fork edin
2. Feature branch oluşturun (`git checkout -b feature/amazing-feature`)
3. Değişikliklerinizi commit edin (`git commit -m 'feat: Add amazing feature'`)
4. Branch'inizi push edin (`git push origin feature/amazing-feature`)
5. Pull Request oluşturun

## 📝 Commit Mesaj Kuralları

Commit mesajlarınızı [Conventional Commits](https://www.conventionalcommits.org/) standardına uygun yazın:

- `feat`: Yeni özellik
- `fix`: Hata düzeltmesi
- `docs`: Dokümantasyon değişiklikleri
- `style`: Kod formatı değişiklikleri
- `refactor`: Kod refactoring
- `test`: Test ekleme veya düzenleme
- `chore`: Yapılandırma değişiklikleri

Örnek:
```
feat: Add new cache driver support
fix: Resolve security vulnerability in auth
docs: Update installation guide
```

## 🔍 Code Review Süreci

1. Pull Request açmadan önce:
   - Testleri çalıştırın
   - Code style kontrolü yapın
   - Dokümantasyonu güncelleyin
   - Branch'inizi güncel tutun

2. Pull Request açarken:
   - Detaylı açıklama ekleyin
   - İlgili issue'ları bağlayın
   - Değişikliklerin ekran görüntülerini ekleyin
   - Test sonuçlarını paylaşın

## 💻 Geliştirme Ortamı

1. Gereksinimleri yükleyin:
```bash
composer install
```

2. Test ortamını hazırlayın:
```bash
cp .env.example .env.testing
php nova key:generate --env=testing
```

3. Testleri çalıştırın:
```bash
composer test
```

4. Code style kontrolü:
```bash
composer cs-check
```

## 🧪 Test Yazma

1. Feature testleri için:
```php
public function test_new_feature()
{
    // Arrange
    $data = ['key' => 'value'];

    // Act
    $response = $this->post('/api/endpoint', $data);

    // Assert
    $response->assertStatus(200);
    $this->assertDatabaseHas('table', $data);
}
```

2. Unit testler için:
```php
public function test_specific_method()
{
    // Arrange
    $class = new MyClass();

    // Act
    $result = $class->method();

    // Assert
    $this->assertEquals('expected', $result);
}
```

## 📚 Dokümantasyon

- Yeni özellikler için dokümantasyon ekleyin
- Var olan dokümantasyonu güncelleyin
- Kod örnekleri ekleyin
- API dokümantasyonunu güncel tutun

## 🔒 Güvenlik

- Güvenlik açıklarını public olarak paylaşmayın
- security@novaphp.dev adresine bildirin
- [Güvenlik Politikası](SECURITY.md)'nı takip edin

## 🎯 Kod Standartları

1. PSR standartlarını takip edin:
   - PSR-1: Basic Coding Standard
   - PSR-2: Coding Style Guide
   - PSR-4: Autoloading Standard
   - PSR-12: Extended Coding Style

2. PHP DocBlock kullanın:
```php
/**
 * Method açıklaması
 *
 * @param string $param Parametre açıklaması
 * @return array
 * @throws \Exception
 */
public function method(string $param): array
```

3. Type hinting kullanın:
```php
public function process(array $data): void
{
    // Implementation
}
```

## 🌟 Issue Oluşturma

1. Bug report için:
   - Hatanın detaylı açıklaması
   - Yeniden üretme adımları
   - Beklenen davranış
   - Gerçekleşen davranış
   - Sistem bilgileri

2. Feature request için:
   - Özelliğin açıklaması
   - Kullanım senaryoları
   - Alternatif çözümler
   - Ek bağlam

## 🤝 Davranış Kuralları

1. Saygılı ve yapıcı iletişim kurun
2. Farklı görüşlere açık olun
3. Yapıcı geri bildirim verin
4. Topluluk yönergelerini takip edin

## 📅 Release Süreci

1. Semantic Versioning takip edin:
   - MAJOR: Uyumsuz API değişiklikleri
   - MINOR: Geriye dönük uyumlu özellikler
   - PATCH: Geriye dönük uyumlu düzeltmeler

2. Release Notes:
   - Değişikliklerin listesi
   - Breaking changes
   - Upgrade notları
   - Teşekkürler

## 🎉 Teşekkürler

Katkılarınız için şimdiden teşekkür ederiz! Her türlü katkı değerlidir ve projenin gelişimine yardımcı olur.
