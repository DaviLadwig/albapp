<?php

$url = "https://nhejeaohnhjvopupshgz.supabase.co/auth/v1/token?grant_type=password";

$data = [
    "email" => "teste@teste.com",
    "password" => "123456"
];

$anon = "SUA_ANON_KEY_AQUI";

$ch = curl_init($url);

curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "apikey: $anon",
        "Authorization: Bearer $anon",
        "Content-Type: application/json"
    ],
    CURLOPT_POSTFIELDS => json_encode($data)
]);

$response = curl_exec($ch);

header('Content-Type: application/json');

echo $response;
