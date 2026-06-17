# CodeIgniter 3 + Docker RESTful API Framework (Daynex B2B Hazırlık)

Bu proje, modern RESTful API standartlarını ve kurumsal backend mimarilerini deneyimlemek amacıyla, **Docker** üzerinde izole edilmiş **PHP 8 + Apache + MySQL** ortamında geliştirilmiş esnek ve hafif bir API altyapısıdır.

---

## 🚨 ÖNEMLİ NOT: Geliştirme Yaklaşımı & AI Kullanımı

> **Bu proje, internetten hazır kopyalanmış veya yapay zekaya (AI) körü körüne yazdırılmış bir "full-AI" projesi DEĞİLDİR.** > 
> Projenin tüm **MVC mimari tasarımı, veritabanı ilişkileri (Foreign Key / Cascade kuralları), CORS politikaları ve JWT güvenlik süreçleri** bizzat benim tarafımdan kurgulanmış, yönetilmiş ve test edilmiştir. Yapay zeka (AI), geliştirme sürecinde sadece kodların refactor edilmesi, PHP 8 uyumluluk optimizasyonları ve dökümantasyon/yorum satırlarının kurumsal standartlara göre revize edilmesi amacıyla akıllı bir asistan olarak kullanılmıştır. Projedeki her bir satır kodun çalışma mantığına, HTTP durum kodlarının seçimine ve mimari kararlara tamamen hakimim.

---

## 🛠️ Teknolojiler ve Altyapı

* **Framework:** CodeIgniter 3 (PHP 8.0 ve üzeri versiyonlarla tamamen uyumlu hale getirilmiştir)
* **Ortam:** Docker & Docker Compose (Çoklu konteyner mimarisi: Web & DB)
* **Veritabanı:** MySQL 8.0 (İlişkisel Veritabanı Yönetimi)
* **Güvenlik / Kimlik Doğrulama:** JWT (JSON Web Token - `firebase/php-jwt`)
* **Bağımlılık Yönetimi:** Composer (Docker konteynerine entegre)

---

## 📐 Mimari ve Öne Çıkan Özellikler

### 1. Ortak API Sınıfı (`Api_Controller`)
Tüm controller sınıflarının türediği `Api_Controller`, projenin kalbini oluşturur:
* **CORS Yönetimi:** Tarayıcı krizlerini önleyen dinamik `OPTIONS` ve header yönetimi.
* **Payload Çözücü:** Ham JSON verilerini otomatik olarak yakalayıp `$this->input_data` dizisine çeviren mekanizma.
* **Standart Çıktı:** Tüm endpoint'lerin istemciye tek tip ve kurumsal standartta (Status, Message, Data) JSON dönmesini sağlayan merkezi `response()` fonksiyonu.

### 2. İlişkisel Veritabanı Tasarımı (SQL JOIN & Cascade)
* `categories` ve `products` tabloları arasında **Foreign Key** ilişkisi kurulmuştur.
* `ON DELETE CASCADE` kuralı sayesinde veri tutarlılığı (`Referential Integrity`) veritabanı seviyesinde güvenceye alınmıştır.
* `Product_model` içinde veriler ham SQL yerine CodeIgniter **Query Builder** ile `INNER JOIN` yapılarak ilişkili şekilde çekilmektedir.

### 3. Esnek ve Seçici API Güvenliği (JWT)
* Geliştirilen `auth_check()` güvenlik duvarı sayesinde, sistemdeki kritik operasyonlar token kontrolüne tabi tutulmuştur.
* Proje ihtiyaçları doğrultusunda **esnek iş mantığı (business logic)** uygulanmıştır: `GET` (Listeleme), `POST` (Ekleme) ve `PUT` (Güncelleme) işlemleri hızlı işlem yapılabilmesi için herkese açık bırakılmış; ancak en kritik operasyon olan `DELETE` (Silme) işlemi **JWT Bearer Token** ile sıkı bir koruma altına alınmıştır.

---

## 🔌 API Endpoint'leri ve Rotalar (Routes)

### Kimlik Doğrulama (Auth)
* `POST /index.php/auth/login` -> Kullanıcı adı ve şifre doğrulaması yaparak **JWT Token** üretir.

### Kategoriler (Category)
* `GET /index.php/category` -> Tüm kategorileri listeler.
* `POST /index.php/category/create` -> Yeni kategori ekler.
* `PUT /index.php/category/update/(:num)` -> Belirtilen ID'deki kategoriyi günceller.
* `DELETE /index.php/category/delete/(:num)` -> Belirtilen ID'deki kategoriyi siler.

### Ürünler (Product)
* `GET /index.php/product` -> Tüm ürünleri, bağlı oldukları kategori adıyla (`JOIN`) birlikte listeler.
* `GET /index.php/product/detail/(:num)` -> Belirli bir ürünün detayını getirir.
* `POST /index.php/product/create` -> Yeni ürün ekler (Geçerli bir `category_id` zorunludur).
* `PUT /index.php/product/update/(:num)` -> Ürün bilgilerini günceller.
* `DELETE /index.php/product/delete/(:num)` -> **[KORUMALI]** Ürünü siler. İstek atılırken Header'da geçerli bir `Authorization: Bearer <token>` gönderilmesi zorunludur.

---

## 🚀 Kurulum ve Çalıştırma

Projenin Docker altyapısı sayesinde tek bir komutla tüm sistemi ayağa kaldırabilirsiniz:

```bash
# Konteynerleri inşa et ve arka planda çalıştır
docker-compose up -d --build

# Veritabanı tablolarının oluşturulması için Docker içerisindeki MySQL'e bağlanıp SQL sorgularını çalıştırabilirsiniz.
