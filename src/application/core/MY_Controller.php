<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class MY_Controller extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
}

class Api_Controller extends MY_Controller {

    protected $input_data;
    // JWT şifrelemesinde kullanılacak gizli anahtar
    private $jwt_secret = 'daynex_b2b_saas_api_gizli_saf_jwt_anahtari_2026_super_secret!';

    public function __construct() {
        parent::__construct();

        // CORS Ayarları (X-Authorization ve x-authorization izin listesine eklendi)
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding, Authorization, X-Authorization, x-authorization, X-Requested-With");
        
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            $this->output->set_status_header(200)->_display();
            exit;
        }

        // Ham JSON Payload yakalama
        $raw_input = file_get_contents('php://input');
        $this->input_data = json_decode($raw_input, true) ?? [];
    }

    /**
     * Dışarıya Token Üretme Fonksiyonu
     */
    protected function generate_token($user_data) {
        $issued_at = time();
        $payload = [
            'iat'  => $issued_at,
            'exp'  => $issued_at + 3600, // 1 Saat geçerlilik
            'user' => $user_data
        ];
        return JWT::encode($payload, $this->jwt_secret, 'HS256');
    }

    /**
     * KORUMA DUVARI (Geliştirilmiş Kurşun Geçirmez Sürüm)
     */
    protected function auth_check() {
        $headers = null;

        // 1. İhtimal: Standart Authorization başlığına bak
        $headers = $this->input->get_request_header('Authorization', TRUE);

        // 2. İhtimal: X-Authorization başlığına bak
        if (!$headers) {
            $headers = $this->input->get_request_header('X-Authorization', TRUE);
        }

        // 3. İhtimal: Küçük harfli x-authorization başlığına bak
        if (!$headers) {
            $headers = $this->input->get_request_header('x-authorization', TRUE);
        }

        if (!$headers) {
            $this->response(false, 'Yetkilendirme başlığı bulunamadı. Lütfen giriş yapın.', null, 401);
        }

        // Eğer gelen değer direkt ham token ise veya Bearer içeriyorsa parçala
        if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
            $token = $matches[1];
        } else {
            // Eğer Bearer yazılmadan direkt ham token gönderildiyse doğrudan kabul et
            $token = trim($headers);
        }

        if (!$token || $token === 'null') {
            $this->response(false, 'Geçersiz veya boş Token formatı.', null, 401);
        }

        try {
            // Token'ı çözüyoruz
            $decoded = JWT::decode($token, new Key($this->jwt_secret, 'HS256'));
            return $decoded->user;

        } catch (Exception $e) {
            $this->response(false, 'Geçersiz veya süresi dolmuş Token! Giriş reddedildi.', null, 401);
        }
    }

    /**
     * Standart JSON Çıktı Fonksiyonu
     */
    protected function response($status = true, $message = '', $data = null, $status_code = 200) {
        $response = ['status' => $status, 'message' => $message];
        if ($data !== null) { $response['data'] = $data; }

        $this->output
             ->set_status_header($status_code)
             ->set_content_type('application/json', 'utf-8')
             ->set_output(json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
             ->_display();
        exit;
    }
}