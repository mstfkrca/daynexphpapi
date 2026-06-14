<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_model extends CI_Model {

    // 1. Tüm Ürünleri Kategorisiyle Birlikte Çekme (SQL JOIN)
    public function get_all() {
        // Hangi kolonları seçeceğimizi belirtiyoruz
        $this->db->select('products.*, categories.name as category_name');
        
        // Ana tablomuz products
        $this->db->from('products');
        
        // categories tablosunu category_id üzerinden bağlıyoruz (INNER JOIN)
        $this->db->join('categories', 'categories.id = products.category_id');
        
        $query = $this->db->get();
        return $query->result_array();
    }

    // 2. Tek Bir Ürünü Detayıyla Çekme
    public function get_by_id($id) {
        $this->db->select('products.*, categories.name as category_name');
        $this->db->from('products');
        $this->db->join('categories', 'categories.id = products.category_id');
        $this->db->where('products.id', $id);
        
        $query = $this->db->get();
        return $query->row_array(); // Tek satır döneceği için row_array() kullandık
    }

    // 3. Yeni Ürün Ekleme
    public function insert($data) {
        $this->db->insert('products', $data);
        return $this->db->insert_id();
    }

    // 4. Ürün Güncelleme
    public function update($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('products', $data);
    }

    // 5. Ürün Silme
    public function delete($id) {
        $this->db->where('id', $id);
        return $this->db->delete('products');
    }
}