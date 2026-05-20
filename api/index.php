<?php
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$page = trim($path, '/');

if ($page === '') {
    $page = 'index.php';
}

$allowedPages = [
    'index.php',
    'dashboard.php',
    'orcamentos.php',
    'financeiro.php',
    'configuracoes.php',
];

if (!in_array($page, $allowedPages, true)) {
    http_response_code(404);
    echo 'Página não encontrada.';
    exit;
}

if ($page !== 'index.php' && empty($_COOKIE['alb_logged_in'])) {
    header('Location: /index.php');
    exit;
}

require dirname(__DIR__) . '/' . $page;
?>
