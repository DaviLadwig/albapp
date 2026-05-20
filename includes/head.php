<?php require_once __DIR__ . '/config.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#05011B">
    <title><?php echo $pageTitle ?? $appName; ?></title>
    <link rel="manifest" href="manifest.webmanifest">
    <link rel="icon" href="assets/img/app-icon.png" type="image/png">
    <link rel="apple-touch-icon" href="assets/img/app-icon.png">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body class="<?php echo $bodyClass ?? ''; ?>">
