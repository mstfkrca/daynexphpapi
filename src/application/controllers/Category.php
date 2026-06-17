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
        
        // Alınan veriyi JSON formatında istemciye dönüyoruz
        $this->response(true, 'Kategoriler başarıyla listelendi.', $categories, 200);
    }

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

        // ----------------------------------------------------------------
        // KURUMSAL KONTROL: Aynı isimde kategori var mı bakıyoruz (Değişken ismi düzeltildi)
        // ----------------------------------------------------------------
        $this->db->where('name', $category_name);
        $exists = $this->db->get('categories')->num_rows();

        if ($exists > 0) {
            // Eğer varsa 500 çökmek yerine şık bir 400 Bad Request dönüyoruz
            $this->response(false, 'Bu kategori adı zaten mevcut!', null, 400);
        }
        // ----------------------------------------------------------------

        // Veri tabanındaki kolon isimleriyle eşleşen dizimizi hazırlıyoruz
        $db_data = [
            'name'        => $category_name,
            'description' => $category_desc
        ];

        // Model vasıtasıyla veriyi MySQL'e yazıyoruz (Mükerrer satır silindi, tek sefer ekliyor)
        $inserted_id = $this->Category_model->insert($db_data);

        // İstemciye kaydettiğimiz veriyi ID'si ile birlikte geri göstermek için diziyi güncelliyoruz
        $db_data['id'] = $inserted_id;

        // 201 Created koduyla başarılı yanıtı dönüyoruz
        $this->response(true, 'Kategori başarıyla oluşturuldu.', $db_data, 201);
    }

    // DELETE: http://localhost:8080/index.php/category/delete/[ID]
    public function delete($id = null) {
        if ($this->input->method(TRUE) !== 'DELETE') {
            $this->response(false, 'Geçersiz istek yöntemi. Sadece DELETE kabul edilir.', null, 405);
        }

        if (!$id) {
            $this->response(false, 'Silinecek kategori ID\'si belirtilmedi.', null, 400);
        }

        $this->Category_model->delete($id);
        $this->response(true, 'Kategori başarıyla silindi. ID: ' . $id, null, 200);
    }

    // PUT: http://localhost:8080/index.php/category/update/[ID]
    public function update($id = null) {
        if ($this->input->method(TRUE) !== 'PUT') {
            $this->response(false, 'Geçersiz istek yöntemi. Sadece PUT kabul edilir.', null, 405);
        }

        if (!$id) {
            $this->response(false, 'Güncellenecek kategori ID\'si belirtilmedi.', null, 400);
        }

        $category_name = $this->input_data['name'] ?? null;
        $category_desc = $this->input_data['description'] ?? null;

        if (!$category_name) {
            $this->response(false, 'Güncelleme için kategori adı zorunuludur.', null, 400);
        }

        $update_data = [
            'name'        => $category_name,
            'description' => $category_desc
        ];

        $this->Category_model->update($id, $update_data);
        $update_data['id'] = $id;

        $this->response(true, 'Kategori başarıyla güncellendi.', $update_data, 200);
    }
}