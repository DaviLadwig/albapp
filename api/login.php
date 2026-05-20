<?php
require_once __DIR__ . '/../includes/supabase.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    json_response(['error' => 'Método não permitido.']);
    exit;
}

$data = json_input();
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

if ($email === '' || $password === '') {
    http_response_code(422);
    json_response(['error' => 'Informe email e senha.']);
    exit;
}

$response = supabase_password_login($email, $password);

if (!empty($response['access_token'])) {
    setcookie('alb_logged_in', '1', [
        'expires' => time() + 60 * 60 * 12,
        'path' => '/',
        'secure' => !empty($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

json_response($response);
?>
