<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Category_model extends CI_Model {

    // PHP 8 uyumluluğu için constructor'ı (__construct) tamamen kaldırdık.
    // CI_Model arkada kendi işlerini hallediyor, bizim doğrudan fonksiyonlara odaklanmamız yeterli.

    // 1. Tüm Kategorileri Çeken Fonksiyon
    public function get_all() {
        // SELECT * FROM categories sorgusunu çalıştırır
        $query = $this->db->get('categories');
        
        // Sonuçları array (dizi) formatında geri döndürür
        return $query->result_array();
    }

    // 2. Yeni Kategori Ekleyen Fonksiyon
    public function insert($data) {
        // INSERT INTO categories (...) VALUES (...) sorgusunu çalıştırır
        $this->db->insert('categories', $data);
        
        // MySQL'in bu yeni satıra verdiği otomatik artan ID'yi geri döner
        return $this->db->insert_id();
    }

    // Dışarıdan gelen ID bilgisine göre veri tabanından satır siler
    public function delete($id) {
        // Hangi satırın silineceğini filtreliyoruz: WHERE id = $id
        $this->db->where('id', $id);
        
        // categories tablosundan bu kurala uyan satırı uçuruyoruz
        return $this->db->delete('categories');
    }

    // Dışarıdan gelen ID'ye sahip satırı, gelen yeni verilerle ($data) günceller
    public function update($id, $data) {
        // Hangi satırın güncelleneceğini seçiyoruz: WHERE id = $id
        $this->db->where('id', $id);
        
        // categories tablosundaki o satırı yeni veri paketiyle güncelliyoruz
        // Arka planda "UPDATE categories SET name = ..., description = ... WHERE id = ..." çalışır
        return $this->db->update('categories', $data);
    }
} 