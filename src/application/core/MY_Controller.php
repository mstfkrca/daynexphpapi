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
    // JWT şifrelemesinde kullanılacak gizli anahtar (Daynex'te bunu config'e taşırlar)
    private $jwt_secret = 'daynex_b2b_saas_api_gizli_saf_jwt_anahtari_2026_super_secret!';

    public function __construct() {
        parent::__construct();

        // CORS Ayarları
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding, Authorization, X-Requested-With");
        
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            $this->output->set_status_header(200)->_display();
            exit;
        }

        // Ham JSON Payload yakalama
        $raw_input = file_get_contents('php://input');
        $this->input_data = json_decode($raw_input, true) ?? [];
    }

    /**
     * Dışarıya Token Üretme Fonksiyonu (Giriş yapıldığında çağrılacak)
     */
    protected function generate_token($user_data) {
        $issued_at = time();
        $payload = [
            'iat'  => $issued_at,            // Token'ın üretilme zamanı
            'exp'  => $issued_at + 3600,     // Token'ın ömrü (1 Saat = 3600 saniye)
            'user' => $user_data             // İçine koyacağımız kullanıcı bilgileri
        ];

        // HS256 algoritmasıyla şifreleyip string token dönüyoruz
        return JWT::encode($payload, $this->jwt_secret, 'HS256');
    }

    /**
     * KORUMA DUVARI: Bu fonksiyonu çağıran endpoint'e token'sız girilemez!
     */
    protected function auth_check() {
        // Gelen isteklerin Header (Başlık) kısmından Authorization alanını yakalıyoruz
        $headers = $this->input->get_request_header('Authorization', TRUE);

        if (!$headers) {
            $this->response(false, 'Yetkilendirme başlığı bulunamadı. Lütfen giriş yapın.', null, 401);
        }

        // Header genellikle "Bearer <token>" şeklinde gelir. Aradaki boşluktan token'ı ayırıyoruz.
        $token_parts = explode(" ", $headers);
        $token = $token_parts[1] ?? null;

        if (!$token) {
            $this->response(false, 'Geçersiz Token formatı.', null, 401);
        }

        try {
            // Token'ı gizli anahtarımızla çözmeye çalışıyoruz
            $decoded = JWT::decode($token, new Key($this->jwt_secret, 'HS256'));
            
            // Eğer token sağlamsa, içindeki kullanıcı bilgilerini controller kullansın diye return ediyoruz
            return $decoded->user;

        } catch (Exception $e) {
            // Token'ın süresi bittiyse veya sahteyse burası yakalar
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