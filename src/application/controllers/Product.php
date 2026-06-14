<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product extends Api_Controller {

    public function __construct() {
        parent::__construct();
        // Ürün modelini yüklüyoruz
        $this->load->model('Product_model');
    }

    // GET: http://localhost:8080/index.php/product
    public function index() {
        if ($this->input->method(TRUE) !== 'GET') {
            $this->response(false, 'Geçersiz metot.', null, 405);
        }

        $products = $this->Product_model->get_all();
        $this->response(true, 'Ürünler kategorileriyle birlikte listelendi.', $products, 200);
    }

    // GET: http://localhost:8080/index.php/product/detail/[ID]
    public function detail($id = null) {
        if ($this->input->method(TRUE) !== 'GET') {
            $this->response(false, 'Geçersiz metot.', null, 405);
        }

        if (!$id) {
            $this->response(false, 'Ürün ID belirtilmedi.', null, 400);
        }

        $product = $this->Product_model->get_by_id($id);

        if (!$product) {
            $this->response(false, 'Ürün bulunamadı.', null, 404);
        }

        $this->response(true, 'Ürün detayı getirildi.', $product, 200);
    }

    // POST: http://localhost:8080/index.php/product/create
    public function create() {
        if ($this->input->method(TRUE) !== 'POST') {
            $this->response(false, 'Geçersiz metot.', null, 405);
        }

        $category_id   = $this->input_data['category_id'] ?? null;
        $product_name  = $this->input_data['name'] ?? null;
        $product_price = $this->input_data['price'] ?? null;
        $product_stock = $this->input_data['stock'] ?? 0;

        // Zorunlu alan kontrolü (Validasyon)
        if (!$category_id || !$product_name || !$product_price) {
            $this->response(false, 'Eksik parametre! category_id, name ve price zorunludur.', null, 400);
        }

        $db_data = [
            'category_id' => $category_id,
            'name'        => $product_name,
            'price'       => $product_price,
            'stock'       => $product_stock
        ];

        $inserted_id = $this->Product_model->insert($db_data);
        $db_data['id'] = $inserted_id;

        $this->response(true, 'Ürün başarıyla oluşturuldu.', $db_data, 201);
    }

    // PUT: http://localhost:8080/index.php/product/update/[ID]
    public function update($id = null) {
        if ($this->input->method(TRUE) !== 'PUT') {
            $this->response(false, 'Geçersiz metot.', null, 405);
        }

        if (!$id) {
            $this->response(false, 'Ürün ID belirtilmedi.', null, 400);
        }

        $update_data = [
            'category_id' => $this->input_data['category_id'] ?? null,
            'name'        => $this->input_data['name'] ?? null,
            'price'       => $this->input_data['price'] ?? null,
            'stock'       => $this->input_data['stock'] ?? null
        ];

        // Boş bırakılan alanları diziden temizleyelim ki mevcut veriyi bozmasın
        $update_data = array_filter($update_data, function($value) { return $value !== null; });

        if (empty($update_data)) {
            $this->response(false, 'Güncellenecek veri gönderilmedi.', null, 400);
        }

        $this->Product_model->update($id, $update_data);
        $update_data['id'] = $id;

        $this->response(true, 'Ürün başarıyla güncellendi.', $update_data, 200);
    }

    // DELETE: http://localhost:8080/index.php/product/delete/[ID]
    public function delete($id = null) {
        // GÜVENLİK DUVARI: İstek geldiği an ilk iş olarak token kontrol et!
        // Eğer token yoksa veya geçersizse bu fonksiyon içerideki $this->response vasıtasıyla 
        // uygulamayı çat diye durduracak (exit) ve alttaki silme kodlarına ASLA geçirmeyecektir.
        $this->auth_check();

        if ($this->input->method(TRUE) !== 'DELETE') {
            $this->response(false, 'Geçersiz metot.', null, 405);
        }

        if (!$id) {
            $this->response(false, 'Ürün ID belirtilmedi.', null, 400);
        }

        // Eğer yukarıdaki auth_check() duvarını aşabilirse ancak o zaman buraya gelir:
        $this->Product_model->delete($id);
        $this->response(true, 'Ürün başarıyla silindi. ID: ' . $id, null, 200);
    }
}