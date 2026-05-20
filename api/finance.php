<?php
require_once __DIR__ . '/../includes/supabase.php';
require_bearer_token();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    json_response(supabase_request('GET', 'finance_entries?select=*&order=entry_date.desc,created_at.desc'));
    exit;
}

if ($method === 'POST') {
    $data = json_input();
    $payload = [
        'entry_date' => $data['date'] ?? date('Y-m-d'),
        'description' => $data['desc'] ?? 'Lançamento',
        'type' => $data['type'] ?? 'entrada',
        'value' => (float) ($data['value'] ?? 0),
    ];

    json_response(supabase_request('POST', 'finance_entries', $payload, [
        'Prefer: return=representation',
    ]));
    exit;
}

if ($method === 'DELETE') {
    $id = $_GET['id'] ?? '';
    json_response(supabase_request('DELETE', 'finance_entries?id=eq.' . rawurlencode($id), null, [
        'Prefer: return=representation',
    ]));
    exit;
}

http_response_code(405);
json_response(['error' => 'Método não permitido.']);
?>
