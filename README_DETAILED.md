# 📊 Yatırım Fonları Backend API

Modern ve güvenli bir Laravel 12 API - TEFAS bilgi sistemi.

---

## 🎯 Genel Yapı

Backend, mobil uygulamaya (React Native) REST API servisleri sunmaktadır. 
- **Framework**: Laravel 12
- **Database**: SQLite (varsayılan)
- **Authentication**: Sanctum API Tokens
- **Frontend**: Blade Templates + Vite + Tailwind CSS

---

## 📁 Proje Dizini Yapısı

```
backend/
├── app/                          # Uygulama kodu
│   ├── Http/Controllers/         # API Controllers (8)
│   │   ├── UserController.php
│   │   ├── TefasFundController.php
│   │   ├── FundStatsHistoryController.php
│   │   ├── TefasBestCategoryRateController.php
│   │   ├── TefasBestFundRateController.php
│   │   ├── TefasComparisonHistoryController.php
│   │   ├── TefasFundDetailController.php
│   │   └── FavoriteFundController.php
│   │
│   ├── Models/                   # Veritabanı Modelleri (10)
│   │   ├── User.php              # Kullanıcı (Sanctum Auth)
│   │   ├── TefasFund.php         # Fon Ana Verileri
│   │   ├── TefasCategory.php     # Fon Kategorileri
│   │   ├── TefasPeriod.php       # Performans Dönemleri (1ay, 3ay, vb)
│   │   ├── FundStatsHistory.php  # Fon İstatistikleri (Geçmiş)
│   │   ├── TefasFundDetail.php   # Portfolyo Dağılımı
│   │   ├── TefasBestCategoryRate.php   # Kategori Verimliliği
│   │   ├── TefasBestFundRate.php       # En İyi 9 Fon
│   │   ├── TefasComparisonHistory.php  # Karşılaştırma Verileri
│   │   └── UserFavoriteFund.php        # Favori Fonlar (Pivot)
│   │
│   ├── Providers/                # Service Providers
│   │
├── routes/
│   ├── api.php                   # API Rotaları (18 endpoint)
│   └── web.php                   # Web Rotaları (Blade)
│
├── database/
│   ├── migrations/               # Veritabanı Şemaları (6)
│   ├── seeders/                  # Veri Ekleyen Scriptler
│   └── factories/                # Test Data Generators
│
├── config/
│   ├── app.php                   # Uygulama Konfigürasyonu
│   ├── database.php              # Veritabanı Konfigürasyonu
│   ├── auth.php                  # Authentication (Sanctum)
│   └── ...
│
├── resources/
│   ├── views/                    # Blade Şablonları
│   ├── css/                      # Tailwind CSS
│   └── js/                       # JavaScript
│
├── storage/
│   ├── app/                      # Dosya Depolama
│   ├── logs/                     # Uygulama Logları
│   └── framework/                # Framework Cache
│
├── tests/                        # PHPUnit Testleri
│
├── bootstrap/                    # Framework Bootstrap
│
├── public/
│   └── index.php                 # Giriş Noktası
│
├── vite.config.js               # Vite Konfigürasyonu
├── composer.json                # PHP Bağımlılıkları
├── package.json                 # Node.js Bağımlılıkları
├── phpunit.xml                  # Test Konfigürasyonu
└── artisan                       # CLI Komutları

```

---

## 🗄️ Veritabanı Modelleri

### 1. **User** (Kullanıcı)
Kimlik doğrulama ve profil yönetimi.

```
- id (PK)
- name (string)
- email (unique)
- password (hashed)
- created_at
- updated_at

Relationships:
├── hasMany('UserFavoriteFund') → Favori Fonlar
```

### 2. **TefasFund** (Fon Ana Verileri)
Yatırım fonları bilgileri ve özellikleri.

```
- code (PK, string) - "HAK", "YAT", vs
- name (string) - Fon Adı
- category_id (FK) - Kategori
- isin_code (string)
- entry_commission (decimal) - Giriş Komisyonu
- exit_commission (decimal) - Çıkış Komisyonu
- risk_value (integer)
- fon_varlık_dagılım_list (json)
- fon_varlık_dagılım_degerler (json)
... (30+ alan)

Relationships:
├── belongsTo('TefasCategory')
├── hasMany('FundStatsHistory')
├── hasMany('TefasFundDetail')
```

### 3. **TefasCategory** (Fon Kategorileri)
Yatırım türleri: Hisse Senedi, Sabit Getirili, Para Piyasası vb.

```
- id (PK)
- name (string) - "Hisse Senedi Fonu", "Sabit Getirili Fonu", vs

Relationships:
├── hasMany('TefasFund')
├── hasMany('TefasBestCategoryRate')
├── hasMany('TefasBestFundRate')
```

### 4. **TefasPeriod** (Performans Dönemleri)
Verimlilik karşılaştırma dönemleri: 1 ay, 3 ay, 6 ay, 1 yıl.

```
- id (PK, no auto-increment) - 1, 3, 6, 12, 24 (ay)
- period_name (string) - "1 Aylık", "3 Aylık", vs

Relationships:
├── hasMany('TefasBestCategoryRate')
├── hasMany('TefasBestFundRate')
├── hasMany('TefasComparisonHistory')
```

### 5. **FundStatsHistory** (Fon İstatistikleri)
Fonların tarihsel performans verileri (günlük güncelleme).

```
Composite PK: (code + created_at)

- code (string, PK) - Fon kodu
- created_at (date, PK) - Veri tarihi
- last_price (decimal) - Son Fiyat
- daily_return (decimal) - Günlük Getiri
- return_1m/3m/6m/12m (decimal) - 1,3,6,12 aylık getiriler
- shares_outstanding (big integer) - Dolaşımdaki pay
- total_value (decimal) - Toplam Değer
- investor_count (integer) - Yatırımcı Sayısı
- market_share (decimal) - Pazar Payı
- category_rank (integer) - Kategori İçinde Sıralaması

Relationships:
└── belongsTo('TefasFund', 'code', 'code')
```

### 6. **TefasFundDetail** (Portfolyo Dağılımı)
Fonların varlık dağılımı (50+ portfolyo tipi).

```
Composite PK: (code + tarih)

- code (string, PK) - Fon kodu
- tarih (date, PK) - Tarih
- BB (decimal) - Borsa Bileşenı %
- BPP (decimal) - Bağlı Portföy %
- DT (decimal) - Döviz Tahvili %
- GSYY (decimal) - Gayri Safi Yurt İçi Gelir %
... (45+ daha)

Relationships:
└── belongsTo('TefasFund', 'code', 'code')
```

### 7. **TefasBestCategoryRate** (Kategori Verimliliği)
Her dönem + kategori kombinasyonu için en iyi getiri.

```
- id (PK)
- category_id (FK)
- period_id (FK)
- getiri (decimal) - Kategori Getirisi
- fetched_at (date) - Veri Tarihi

Relationships:
├── belongsTo('TefasCategory')
└── belongsTo('TefasPeriod')
```

### 8. **TefasBestFundRate** (İlk 9 Fon)
Her dönem + kategori için en iyi 9 fon.

```
- id (PK)
- category_id (FK)
- period_id (FK)
- fund_code (FK)
- rank (integer) - 1-9 arası
- getiri (decimal) - Fonun Getriri
- fetched_at (date)

Relationships:
├── belongsTo('TefasCategory')
├── belongsTo('TefasPeriod')
└── belongsTo('TefasFund')
```

### 9. **TefasComparisonHistory** (Karşılaştırma)
Fon karşılaştırması verileri (dönem bazlı).

```
- id (PK)
- code (FK) - Fon kodu
- period_id (FK)
- karsilastirma_verisi (json) - Karşılaştırma detayları
- fetched_at (date)

Relationships:
├── belongsTo('TefasFund')
└── belongsTo('TefasPeriod')
```

### 10. **UserFavoriteFund** (Pivot: Favori Fonlar)
Kullanıcıların favori fonları (many-to-many).

```
- id (PK)
- user_id (FK)
- fund_code (FK)
- created_at (eklenme tarihi)

Relationships:
├── belongsTo('User')
└── belongsTo('TefasFund')
```

---

## 🔌 API Endpoints (18 adet)

### 🔐 Kimlik Doğrulama (Public)

#### `POST /api/register`
Yeni kullanıcı kayıt.

```json
Request:
{
  "name": "Ahmet Yılmaz",
  "email": "ahmet@example.com",
  "password": "secure_password",
  "password_confirmation": "secure_password"
}

Response (201):
{
  "success": true,
  "message": "Kayıt başarılı!",
  "user": { ... }
}
```

#### `POST /api/login`
Giriş ve token oluştur.

```json
Request:
{
  "email": "ahmet@example.com",
  "password": "secure_password"
}

Response (200):
{
  "success": true,
  "user": { ... },
  "token": "eyJ0eXAiOiJKV1QiLCJhbGc..."
}
```

---

### 🔒 Korumalı Endpoints (Sanctum Token Gerekli)
Header: `Authorization: Bearer {token}`

#### **Fon İşlemleri**

##### `GET /api/tefas/funds`
Tüm fonları listele.

```json
Response:
{
  "success": true,
  "data": [
    {
      "code": "HAK",
      "name": "Halkbank Fon Yönetimi",
      "category_name": "Hisse Senedi Fonu"
    },
    ...
  ]
}
```

##### `GET /api/tefas/funds/{code}`
Spesifik fon detayları (tüm alanlar).

```
Örn: GET /api/tefas/funds/HAK
Response: Fon tüm bilgileri (isin, komisyon, risk, varlık dağılımı vb)
```

---

#### **İstatistikler**

##### `GET /api/tefas/fund-stats/{code}`
Fon son istatistikleri.

```
Örn: GET /api/tefas/fund-stats/HAK
Response: {fiyat, günlük getiri, yıllık getiri, yatırımcı sayısı, ...}
```

##### `GET /api/tefas/best-category-rates/period/{periodId}`
Dönem bazlı kategori verimlilikleri.

```
Period IDs: 1=1 ay, 3=3 ay, 6=6 ay, 12=1 yıl, 24=2 yıl
Örn: GET /api/tefas/best-category-rates/period/12
Response: Tüm kategorilerin 1 yıllık getirileri
```

##### `GET /api/tefas/best-fund-rates/category/{categoryId}/period/{periodId}`
Kategori + dönemdeki ilk 9 fon.

```
Örn: GET /api/tefas/best-fund-rates/category/2/period/12
Response: Kategori 2'deki en iyi 9 fonun 1 yıllık getirileri
```

---

#### **Fon Detayları & Portfolyo**

##### `GET /api/tefas/fund-details`
Tüm fonların varlık dağılımı (spesifik tarih).

```
Query Params: ?date=2026-03-28 (opsiyonel, varsayılan: son tarih)
Response: Tüm fonlar ve portfolyo dağılımları
```

##### `GET /api/tefas/fund-details/{code}`
Spesifik fon portfolyo detayları.

```
Query Params: ?date=2026-03-28
Örn: GET /api/tefas/fund-details/HAK?date=2026-03-28
Response: Fon HAK'ın varlık dağılımı (50+ portfolyo tipi)
```

---

#### **Karşılaştırma**

##### `GET /api/tefas/comparison/{code}/period/{periodId}`
Fon karşılaştırma verileri (dönem bazlı).

```
Örn: GET /api/tefas/comparison/HAK/period/12
Response: HAK fonunun 1 yıllık karşılaştırma verileri
```

---

#### **Favori Fon İşlemleri**

##### `GET /api/favorites`
Kullanıcının favori fonlarını listele.

```json
Response:
{
  "success": true,
  "data": [
    {
      "fund_code": "HAK",
      "name": "Halkbank Fon Yönetimi",
      "category_name": "Hisse Senedi Fonu"
    },
    ...
  ],
  "count": 5
}
```

##### `POST /api/favorites/add`
Fonu favorilere ekle.

```json
Request:
{
  "fund_code": "HAK"
}

Response (201):
{
  "success": true,
  "message": "Fon favorilere eklendi"
}
```

##### `POST /api/favorites/check`
Hangi fonlar favoride kontrol et.

```json
Request:
{
  "fund_codes": ["HAK", "YAT", "TBF"]
}

Response:
{
  "success": true,
  "data": {
    "HAK": true,
    "YAT": false,
    "TBF": true
  }
}
```

##### `DELETE /api/favorites/{fundCode}`
Fonu favorilerden çıkar.

```
Örn: DELETE /api/favorites/HAK
Response: { "success": true, "message": "Fon favorilerden çıkarıldı" }
```

---

#### **Kullanıcı**

##### `GET /api/user`
Authenticated kullanıcı bilgisi.

```json
Response:
{
  "id": 1,
  "name": "Ahmet Yılmaz",
  "email": "ahmet@example.com",
  "created_at": "2026-01-15T10:30:00Z"
}
```

##### `POST /api/logout`
Çıkış (token'ı sil).

```json
Response:
{
  "success": true,
  "message": "Başarıyla çıkış yaptınız"
}
```

---

## ⚙️ Configürasyon

### `.env` Dosyası

```
APP_NAME="TEFAS Analytics"
APP_ENV=local
APP_DEBUG=true
APP_KEY=base64:...

# Database (SQLite varsayılan)
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

# API Authentication
SANCTUM_STATELESS_DOMAIN=localhost

# Mail (zorunlu değil, şu an)
MAIL_DRIVER=mailable
```

---

## 🚀 Nasıl Çalıştırılır

### 1️⃣ **Kurulum**

```bash
# Backend klasörüne gir
cd backend

# Bağımlılıkları yükle
composer install
npm install

# .env oluştur
cp .env.example .env
php artisan key:generate

# Database'i başlat
php artisan migrate

# Build işlemi (bir kez)
npm run build
```

### 2️⃣ **Geliştirme Modunda Çalıştırma**

```bash
# Tüm servisleri bağımsız terminallerde çalıştır
npm run dev              # Vite hot-reload
php artisan serve        # Laravel Server (8000)
php artisan queue:listen # Queue Worker
php artisan pail --timeout=0  # Logları göster
```

**VEYA** concurrently kullan:

```bash
composer run dev
```

### 3️⃣ **API Test Etme**

```bash
# Kayıt
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{"name":"Test","email":"test@test.com","password":"password123","password_confirmation":"password123"}'

# Login (token al)
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@test.com","password":"password123"}'

# Protected endpoint (token ile)
curl -X GET http://localhost:8000/api/user \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

## 📊 İş Akışı (Workflow)

### 1. **Kullanıcı Kaydı & Girişi**
```
Mobil App → Register → DB'de User oluştur
         → Login → Sanctum Token oluştur, geri döndür
Token saklanır → Tüm API çağrılarında kullanılır
```

### 2. **Fon Listesi Görüntüleme**
```
App → GET /api/tefas/funds (Token ile)
   → Controller tüm fonları sorgu
   → Kategori bilgisi ile map'le
   → JSON döndür
App → Listeyi render et
```

### 3. **Fon Detayı & Portfolyo Görüntüleme**
```
App → GET /api/tefas/funds/{code}
   → Fon tüm alanlarını döndür

App → GET /api/tefas/fund-details/{code}
   → Portfolyo dağılımını döndür (50+ alan)

App → GET /api/tefas/fund-stats/{code}
   → Son istatistikleri döndür (fiyat, getiri)
```

### 4. **Karşılaştırma & Performans**
```
App seçer → Dönem (1ay, 3ay, 6ay, 1yıl)
         → Kategori (İsteğe bağlı)

GET /api/tefas/best-category-rates/period/{periodId}
   → Tüm kategorilerin verimi
   
GET /api/tefas/best-fund-rates/category/{id}/period/{id}
   → İlk 9 fon
```

### 5. **Favori Yönetimi**
```
Kullanıcı favorite ekler → POST /api/favorites/add
                        → User-Fund ilişkisi kaydedilir

GET /api/favorites       → Favorileri listele

DELETE /api/favorites/{code} → Fon çıkar
```

---

## 🧪 Testing

```bash
# Tüm testleri çalıştır
php artisan test

# Spesifik test dosyasını çalıştır
php artisan test tests/Feature/UserTest.php

# Coverage raporu
php artisan test --coverage
```

---

## 📝 Kontrol Yönetimi & Hata Yönetimi

### Status Kodları
- **200**: Başarılı GET/POST
- **201**: Başarılı CREATE
- **400**: Geçersiz istek (validation)
- **401**: Unauthorized (token yok/geçersiz)
- **404**: Kaynak bulunamadı
- **409**: Conflict (duplicate favorite vb)
- **422**: Validation error (detaylar ile)
- **500**: Server hatası

### Hata Formatı
```json
{
  "success": false,
  "message": "Hata açıklaması",
  "errors": { "field": ["Hata mesajı"] }  // Validation hatası ise
}
```

---

## 🔐 Güvenlik

✅ **Şifre Hash'leme**: Password otomatik bcrypt ile hash'lenir
✅ **API Token**: Sanctum stateless tokens (Bearer)
✅ **SQL Injection**: Eloquent ORM'ile hazırlanmış sorgular
✅ **CORS**: Konfigurasyon dosyasında ayarlanabilir
✅ **Rate Limiting**: Route middleware'inde opsiyonel

---

## 📦 Dependencies

### PHP (Composer)
- `laravel/framework`: ^12.0
- `laravel/sanctum`: ^4.0 (API Auth)
- `laravel/tinker`: ^2.10.1

### Development
- `phpunit/phpunit`: ^11.5.3
- `laravel/pint`: Kod formatlama
- `laravel/pail`: Log viewer

### Node.js (NPM)
- `@tailwindcss/vite`: CSS framework
- `laravel-vite-plugin`: Asset management
- `axios`: HTTP client
- `vite`: Build tool

---

## 🤝 Katkıda Bulunma

1. Feature branş oluştur (`git checkout -b feature/name`)
2. Commit yap (`git commit -am 'Add feature'`)
3. Push et (`git push origin feature/name`)
4. Pull request aç

---

## 📧 İletişim

Sorunlar/öneriler için issue açınız.

---

## 📄 Lisans

MIT Lisansı - Detaylar için LICENSE dosyasını kontrol edin.

---

**Son Güncelleme**: Mart 2026
