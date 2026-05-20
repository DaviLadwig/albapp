<?php
require_once __DIR__ . '/../includes/supabase.php';
require_bearer_token();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    json_response(supabase_request('GET', 'company_settings?id=eq.1&select=*'));
    exit;
}

if ($method === 'POST') {
    $data = json_input();
    $payload = [
        'id' => 1,
        'company_name' => $data['companyName'] ?? 'ALB APP',
        'company_phone' => $data['companyPhone'] ?? '',
        'company_address' => $data['companyAddress'] ?? '',
        'company_pix' => $data['companyPix'] ?? '',
        'company_logo' => $data['companyLogo'] ?? '',
        'updated_at' => date('c'),
    ];

    json_response(supabase_request('POST', 'company_settings', $payload, [
        'Prefer: resolution=merge-duplicates,return=representation',
    ]));
    exit;
}

http_response_code(405);
json_response(['error' => 'Método não permitido.']);
?>
