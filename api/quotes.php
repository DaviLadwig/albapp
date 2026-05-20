<?php
require_once __DIR__ . '/../includes/supabase.php';
require_bearer_token();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    json_response(supabase_request('GET', 'quotes?select=*,quote_items(*)&order=created_at.desc'));
    exit;
}

if ($method === 'POST') {
    $data = json_input();
    $items = $data['items'] ?? [];
    $quotePayload = [
        'client_name' => $data['clientName'] ?? 'Cliente sem nome',
        'client_phone' => $data['clientPhone'] ?? '',
        'client_address' => $data['clientAddress'] ?? '',
        'service_description' => $data['serviceDescription'] ?? '',
        'subtotal' => (float) ($data['subtotal'] ?? 0),
        'discount' => (float) ($data['discount'] ?? 0),
        'total' => (float) ($data['total'] ?? 0),
        'status' => $data['status'] ?? 'Pendente',
    ];

    $quote = supabase_request('POST', 'quotes', $quotePayload, ['Prefer: return=representation']);
    if (isset($quote['error']) || empty($quote[0]['id'])) {
        json_response($quote);
        exit;
    }

    $quoteId = $quote[0]['id'];
    $itemPayload = array_map(function ($item) use ($quoteId) {
        return [
            'quote_id' => $quoteId,
            'service' => $item['service'] ?? 'Serviço',
            'qty' => (float) ($item['qty'] ?? 1),
            'price' => (float) ($item['price'] ?? 0),
        ];
    }, $items);

    if (!empty($itemPayload)) {
        supabase_request('POST', 'quote_items', $itemPayload, ['Prefer: return=representation']);
    }

    json_response($quote);
    exit;
}

http_response_code(405);
json_response(['error' => 'Método não permitido.']);
?>
