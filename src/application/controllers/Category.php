<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Category extends Api_Controller {

    // 1. Constructor: Sınıf ilk çağrıldığında çalışır ve modelimizi yükler
    public function __construct() {
        parent::__construct();
        // Model dosyamızı controller'a enjekte ediyoruz
        $this->load->model('Category_model');
    }

    // 2. Index Fonksiyonu (GET): Tüm kategorileri listeler
    // URL: http://localhost:8080/index.php/category
    public function index(){
        // Modelimizdeki get_all() fonksiyonunu tetikleyip verileri alıyoruz
        $categories = $this->Category_model->get_all();
        
        // Alınan veriyi JSON formatında istemciye (Postman'e) dönüyoruz
        $this->response(true, 'Kategoriler başarıyla listelendi.', $categories, 200);
    } // <-- index fonksiyonunun bittiği yer

    // 3. Create Fonksiyonu (POST): Yeni kategori ekler
    // URL: http://localhost:8080/index.php/category/create
    public function create(){
        // HTTP Metot kontrolü (Sadece POST isteklerini kabul et)
        if ($this->input->method(TRUE) !== 'POST') {
            $this->response(false, 'Geçersiz istek yöntemi.', null, 405);
        }

        // Api_Controller'ın bize hazır çözüp verdiği JSON verisinden alanları alıyoruz
        $category_name = $this->input_data['name'] ?? null;
        $category_desc = $this->input_data['description'] ?? null;
        
        // Validasyon: Kategori adı boş gelemez, boşsa 400 Bad Request dön
        if (!$category_name) {
            $this->response(false, 'Kategori adı sağlanmadı.', null, 400);
        }

        // Veri tabanındaki kolon isimleriyle eşleşen dizimizi hazırlıyoruz
        $db_data = [
            'name'        => $category_name,
            'description' => $category_desc
        ];

        // Model vasıtasıyla veriyi MySQL'e yazıyoruz ve MySQL'in verdiği auto_increment ID'yi alıyoruz
        $inserted_id = $this->Category_model->insert($db_data);

        // İstemciye kaydettiğimiz veriyi ID'si ile birlikte geri göstermek için diziyi güncelliyoruz
        $db_data['id'] = $inserted_id;

        // 201 Created koduyla başarılı yanıtı dönüyoruz
        $this->response(true, 'Kategori başarıyla veri tabanına kaydedildi.', $db_data, 201);
    } // <-- create fonksiyonunun bittiği yer

    // DELETE: http://localhost:8080/index.php/category/delete/[ID]
    // CodeIgniter 3'te URL'deki dinamik parametreyi (ID) fonksiyonun içine değişken olarak alırız ($id)
    public function delete($id = null) {
        // 1. HTTP Yöntem kontrolü (Sadece DELETE isteklerini kabul et)
        if ($this->input->method(TRUE) !== 'DELETE') {
            $this->response(false, 'Geçersiz istek yöntemi. Sadece DELETE kabul edilir.', null, 405);
        }

        // 2. Güvenlik/Validasyon Kontrolü: Eğer URL'de bir ID gönderilmediyse işlemi durdur
        if (!$id) {
            $this->response(false, 'Silinecek kategori ID\'si belirtilmedi.', null, 400); // 400 Bad Request
        }

        // 3. Modeli çağırıp silme işlemini tetikliyoruz
        $this->Category_model->delete($id);

        // 4. Başarılı yanıtı dönüyoruz
        $this->response(true, 'Kategori başarıyla silindi. ID: ' . $id, null, 200);
    }


    // PUT: http://localhost:8080/index.php/category/update/[ID]
    public function update($id = null) {
        // 1. HTTP Yöntem kontrolü (Sadece PUT isteklerini kabul et)
        if ($this->input->method(TRUE) !== 'PUT') {
            $this->response(false, 'Geçersiz istek yöntemi. Sadece PUT kabul edilir.', null, 405);
        }

        // 2. ID Kontrolü: URL'den bir ID gelmiş mi?
        if (!$id) {
            $this->response(false, 'Güncellenecek kategori ID\'si belirtilmedi.', null, 400);
        }

        // 3. Api_Controller'ın hazır çözdüğü JSON verisinden yeni değerleri alıyoruz
        $category_name = $this->input_data['name'] ?? null;
        $category_desc = $this->input_data['description'] ?? null;

        // Validasyon: En azından ismin dolu gelmesini zorunlu tutalım
        if (!$category_name) {
            $this->response(false, 'Güncelleme için kategori adı zorunuludur.', null, 400);
        }

        // Güncellenecek paketimizi hazırlıyoruz
        $update_data = [
            'name'        => $category_name,
            'description' => $category_desc
        ];

        // 4. Modeli çağırıp veri tabanındaki satırı güncelliyoruz
        $this->Category_model->update($id, $update_data);

        // İstemciye güncel halini geri gösterelim
        $update_data['id'] = $id;

        // 5. Başarılı yanıtı dönüyoruz (200 OK)
        $this->response(true, 'Kategori başarıyla güncellendi.', $update_data, 200);
    }


} 