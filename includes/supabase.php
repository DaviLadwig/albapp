<?php
require_once __DIR__ . '/env.php';

function supabase_configured(): bool
{
    return env_value('SUPABASE_URL') !== '' && env_value('SUPABASE_ANON_KEY') !== '';
}

function supabase_request(string $method, string $path, ?array $payload = null, array $headers = []): array
{
    if (!supabase_configured()) {
        http_response_code(503);
        return ['error' => 'Supabase não configurado. Crie o arquivo .env com SUPABASE_URL e SUPABASE_ANON_KEY.'];
    }

    $baseUrl = rtrim(env_value('SUPABASE_URL'), '/');
    $apiKey = env_value('SUPABASE_ANON_KEY');
    $incomingAuth = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    $authHeader = str_starts_with($incomingAuth, 'Bearer ') ? $incomingAuth : 'Bearer ' . $apiKey;
    $url = $baseUrl . '/rest/v1/' . ltrim($path, '/');

    $defaultHeaders = [
        'apikey: ' . $apiKey,
        'Authorization: ' . $authHeader,
        'Content-Type: application/json',
        'Accept: application/json',
    ];

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => array_merge($defaultHeaders, $headers),
    ]);

    if ($payload !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    }

    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($response === false) {
        http_response_code(500);
        return ['error' => $error ?: 'Erro ao conectar ao Supabase.'];
    }

    http_response_code($status ?: 200);
    $decoded = json_decode($response, true);
    return $decoded ?? [];
}

function require_bearer_token(): void
{
    $incomingAuth = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if (!str_starts_with($incomingAuth, 'Bearer ')) {
        http_response_code(401);
        json_response(['error' => 'Login necessário.']);
        exit;
    }
}

function supabase_password_login(string $email, string $password): array
{
    $baseUrl = rtrim(env_value('SUPABASE_URL'), '/');
    $apiKey = trim(env_value('SUPABASE_ANON_KEY'));

    $url = $baseUrl . '/auth/v1/token?grant_type=password';

    $payload = json_encode([
        'email' => trim($email),
        'password' => $password,
    ]);

    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'apikey: ' . $apiKey,
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json',
        ],
        CURLOPT_POSTFIELDS => $payload,
    ]);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        return [
            'error' => curl_error($ch)
        ];
    }

    $decoded = json_decode($response, true);

    return $decoded ?: [
        'error' => 'Resposta inválida'
    ];
}

function json_input(): array
{
    $raw = file_get_contents('php://input');
    return $raw ? json_decode($raw, true) ?? [] : [];
}

function json_response(array $data): void
{
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
}
