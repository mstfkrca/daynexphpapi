<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Category_model extends CI_Model {

    // 1. Tüm Kategorileri Çeken Fonksiyon
    public function get_all() {
        // SELECT * FROM categories sorgusunu çalıştırır
        $query = $this->db->get('categories');
        
        // Sonuçları array (dizi) formatında geri döndürür
        return $query->result_array();
    }

    // 2. Yeni Kategori Ekleyen Fonksiyon (Sadece 1 Kere Tanımlı)
    public function insert($data) {
        // INSERT INTO categories (...) VALUES (...) sorgusunu çalıştırır
        $this->db->insert('categories', $data);
        
        // MySQL'in bu yeni satıra verdiği otomatik artan ID'yi geri döner
        return $this->db->insert_id();
    }

    // 3. Kategori Silen Fonksiyon
    public function delete($id) {
        // Hangi satırın silineceğini filtreliyoruz: WHERE id = $id
        $this->db->where('id', $id);
        
        // categories tablosundan bu kurala uyan satırı uçuruyoruz
        return $this->db->delete('categories');
    }

    // 4. Kategori Güncelleyen Fonksiyon
    public function update($id, $data) {
        // Hangi satırın güncelleneceğini seçiyoruz: WHERE id = $id
        $this->db->where('id', $id);
        
        // categories tablosundaki o satırı yeni veri paketiyle güncelliyoruz
        return $this->db->update('categories', $data);
    }
}