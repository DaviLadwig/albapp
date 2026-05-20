<?php
require_once __DIR__ . '/../includes/supabase.php';

header('Content-Type: application/json');

$data = json_input();

$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

$response = supabase_password_login($email, $password);

echo json_encode([
    'env_url' => env_value('SUPABASE_URL'),
    'env_key_first_chars' => substr(env_value('SUPABASE_ANON_KEY'), 0, 20),
    'email' => $email,
    'response' => $response
], JSON_PRETTY_PRINT);
