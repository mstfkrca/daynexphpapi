# CodeIgniter 3 + Docker RESTful API Framework & jQuery Frontend Panel (Daynex B2B Hazırlık)

Bu proje, modern RESTful API standartlarını, kurumsal backend mimarilerini ve dinamik asenkron (AJAX/jQuery) ön yüz entegrasyonlarını deneyimlemek amacıyla geliştirilmiştir. Docker üzerinde izole edilmiş **PHP 8 + Apache + MySQL** ortamında çalışan esnek bir API altyapısı ile bu API'yi tüketen modern, responsive bir yönetim panelinden oluşur.

---

## 🚨 ÖNEMLİ NOT: Geliştirme Yaklaşımı & AI Kullanımı

Bu proje, internetten hazır kopyalanmış veya yapay zekaya (AI) körü körüne yazdırılmış bir "full-AI" projesi **DEĞİLDİR**. 
* Projenin tüm MVC mimari tasarımı, veritabanı ilişkileri (Foreign Key / Cascade kuralları), CORS politikaları, JWT güvenlik süreçleri ve frontend mimarisi bizzat benim tarafımdan kurgulanmış, yönetilmiş ve test edilmiştir. 
* Yapay zeka (AI), geliştirme sürecinde sadece kodların kurumsal standartlara göre refactor edilmesi, PHP 8 ve tarayıcı cross-platform uyumluluk optimizasyonları ile dökümantasyon/yorum satırlarının temizlenmesi amacıyla akıllı bir asistan olarak kullanılmıştır. 
* Projedeki her bir satır kodun çalışma mantığına, veritabanı performans kriterlerine, HTTP durum kodlarının seçimine ve mimari kararlara tamamen hakimim.

---

## 🛠️ Teknolojiler ve Altyapı

* **Backend Framework:** CodeIgniter 3 (PHP 8.0 ve üzeri versiyonlarla %100 uyumlu hale getirilmiştir)
* **Frontend Altyapısı:** HTML5, CSS3 (Inter Font entegrasyonu), Bootstrap 5 (Responsive Grid & UI Components)
* **Asenkron Veri Yönetimi:** jQuery 3.6.4 (AJAX HTTP Requests & DOM Manipulation)
* **Kullanıcı Deneyimi (UX):** Toastr.js (Non-blocking modern bildirim toast mimarisi)
* **Ortam:** Docker & Docker Compose (Çoklu konteyner mimarisi: Web & DB)
* **Veritabanı:** MySQL 8.0 (İlişkisel Veritabanı Yönetimi & InnoDB Engine)
* **Güvenlik / Kimlik Doğrulama:** JWT (JSON Web Token - `firebase/php-jwt`)
* **Bağımlılık Yönetimi:** Composer (Docker konteynerine entegre)

---

## 📐 Mimari ve Öne Çıkan Özellikler

### 1. Katmanlı ve Temiz Frontend Mimarisi (Separation of Concerns)
Ön yüz kodları tek bir dosyada boğulmak (spagetti kod) yerine, kurumsal standartlara uygun olarak modüler bir şekilde ayrıştırılmıştır:
* `index.html`: Sadece semantik HTML5 iskeletini ve üçüncü parti kütüphanelerin CDN bağlantılarını içerir.
* `style.css`: Kurumsal renk paletini, responsive kırılma noktalarını ve arayüz özelleştirmelerini barındırır.
* `app.js`: Tüm iş mantığını, jQuery AJAX isteklerini, Toastr yönetimini ve form kontrollerini yöneten merkez üssüdür.

### 2. Çift Dikiş Güvenlik ve Mükerrer Kayıt Filtreleme (Business Logic)
* **Frontend Seviyesi:** Kullanıcı yeni bir kategori eklemeye çalıştığında, `app.js` anında canlı select kutusunu tarar. Eğer aynı isimde bir kategori varsa istek API'ye hiç gönderilmez ve `toastr.warning` ile kullanıcı uyarılır.
* **Backend Seviyesi:** Veritabanında `categories` tablosunun `name` alanı `UNIQUE` olarak mühürlenmiştir. Olası bir bypass durumunda `MY_Controller` (Api_Controller) isteği kapıda yakalar ve `400 Bad Request` ile anlamlı bir JSON yanıtı döner. Frontend bu yanıtı `try-catch` mekanizmasıyla parse ederek ekrana kusursuz yansıtır.

### 3. Gizli JWT Panel Modülü ve Güvenli Silme Operasyonu
Sistemde veri listeleme, kategori ve ürün ekleme süreçleri hızlı operasyon için esnek bırakılmış; ancak en kritik süreç olan **DELETE (Ürün Silme)** işlemi sıkı korumaya alınmıştır:
* Arayüze entegre edilen `type="password"` biçimindeki gizli **JWT Giriş Formu**, girilen geçerli tokenı tarayıcının yerel hafızasına (`localStorage`) kaydeder. Sayfa yenilense bile kullanıcıyı yormaz.
* Silme butonuna basıldığında token otomatik olarak AJAX Headers katmanına (`Authorization: Bearer <token>`, `X-Authorization`) enjekte edilir.
* Backend çekirdeği (`MY_Controller.php`) gelen istekleri cross-platform (büyük/küçük harf veya sunucu değişkeni) tarayarak doğrular. Geçersiz durumlarda `401 Unauthorized` koruma duvarı devreye girer.

### 4. İlişkisel Veritabanı Tasarımı (SQL JOIN & Cascade)
* `categories` ve `products` tabloları arasında Foreign Key ilişkisi kurulmuştur. `ON DELETE CASCADE` kuralı sayesinde veri tutarlılığı (Referential Integrity) veritabanı seviyesinde korunur.
* Ürün listelenirken CodeIgniter Query Builder vasıtasıyla `INNER JOIN` yapılarak ilişkili kategorinin adı canlı olarak tabloya basılır.

---

## 🔌 API Endpoint'leri ve Rotalar (Routes)

### Kimlik Doğrulama (Auth)
* `POST /index.php/auth/login` -> Kullanıcı adı (`admin`) ve şifre (`daynex123`) doğrulaması yaparak 1 saat ömürlü JWT Token üretir.

### Kategoriler (Category)
* `GET /index.php/category` -> Tüm kategorileri listeler.
* `POST /index.php/category/create` -> Yeni kategori ekler. *(İsim benzersiz olmak zorundadır).*
* `PUT /index.php/category/update/(:num)` -> Belirtilen ID'deki kategoriyi günceller.
* `DELETE /index.php/category/delete/(:num)` -> Belirtilen ID'deki kategoriyi siler.

### Ürünler (Product)
* `GET /index.php/product` -> Tüm ürünleri, bağlı oldukları kategori adıyla (`INNER JOIN`) birlikte listeler.
* `POST /index.php/product/create` -> Yeni ürün ekler *(Geçerli bir category_id zorunludur)*.
* `PUT /index.php/product/update/(:num)` -> Ürün bilgilerini günceller.
* `DELETE /index.php/product/delete/(:num)` -> **[KORUMALI]** Ürünü siler. İstek atılırken Header katmanında geçerli bir JWT Token gönderilmesi zorunludur.

---

## 🚀 Kurulum, Çalıştırma ve Test Adımları

Projenin Docker altyapısı sayesinde tek bir komutla tüm sistemi ayağa kaldırabilir ve hemen test etmeye başlayabilirsiniz:

### 1. Sistemi Ayağa Kaldırma
```bash
# Konteynerleri inşa et ve arka planda çalıştır
docker-compose up -d --build
```

### 2. Veritabanı Yapılandırması
Docker içerisindeki MySQL terminaline bağlanıp tablolarınızı oluşturun:
```bash
docker exec -it daynex_api_db mysql -u root -p
# (Şifre: rootpassword)
```

### 3. API Güvenlik Duvarını (JWT) Test Etme Adımları
Projedeki **Kalıcı Sil** özelliğini test edebilmek için öncelikle geçerli bir JWT Token üretmeniz gerekmektedir:

1. **Postman'i Açın:** Bir `POST` isteği oluşturun ve adrese `http://localhost:8080/index.php/auth/login` yazın.
2. **Kimlik Bilgilerini Gönderin:** `Body` kısmını `raw` ve `JSON` seçerek şu bilgileri gönderin:
   ```json
   {
     "username": "admin",
     "password": "daynex123"
   }
   ```
3. **Token'ı Kopyalayın:** İstek başarılı olduğunda API size `Giriş başarılı! Token üretildi.` mesajıyla birlikte uzun bir `token` dizesi dönecektir. Bu token değerini tamamen kopyalayın.
4. **Panelde Hafızaya Alın:** Tarayıcıda `index.html` sayfasını açın. Sağ üst köşedeki **🔑 JWT:** alanına bu token'ı yapıştırın ve **Kaydet** butonuna basın (veya Enter'layın).
5. **Silme İşlemini Gerçekleştirin:** Artık tarayıcı hafızasına token kaydedildiği için, tablodan dilediğiniz ürünü güvenlik duvarına takılmadan başarıyla silebilirsiniz.