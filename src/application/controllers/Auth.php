<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends Api_Controller {

    // POST: http://localhost:8080/index.php/auth/login
    public function login() {
        if ($this->input->method(TRUE) !== 'POST') {
            $this->response(false, 'Geçersiz metot.', null, 405);
        }

        $username = $this->input_data['username'] ?? null;
        $password = $this->input_data['password'] ?? null;

        // Basit bir Daynex giriş simülasyonu
        if ($username === 'admin' && $password === 'daynex123') {
            
            // Token içine gömeceğimiz zararsız kullanıcı bilgileri
            $user_payload = [
                'id'   => 10,
                'name' => 'Mustafa Karaca',
                'role' => 'Admin'
            ];

            // Üst sınıftaki token üreticiyi çağırıyoruz
            $token = $this->generate_token($user_payload);

            $this->response(true, 'Giriş başarılı! Token üretildi.', ['token' => $token], 200);
        } else {
            $this->response(false, 'Kullanıcı adı veya şifre hatalı.', null, 401);
        }
    }
}