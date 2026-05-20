<?php
setcookie('alb_logged_in', '', [
    'expires' => time() - 3600,
    'path' => '/',
    'secure' => !empty($_SERVER['HTTPS']),
    'httponly' => true,
    'samesite' => 'Lax',
]);

header('Content-Type: application/json; charset=utf-8');
echo json_encode(['ok' => true]);
?>
