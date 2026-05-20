<?php
$appName = 'ALB APP';

$navItems = [
    ['label' => 'Dashboard', 'href' => 'dashboard.php', 'icon' => 'grid'],
    ['label' => 'Orçamentos', 'href' => 'orcamentos.php', 'icon' => 'file'],
    ['label' => 'Financeiro', 'href' => 'financeiro.php', 'icon' => 'wallet'],
    ['label' => 'Configurações', 'href' => 'configuracoes.php', 'icon' => 'settings'],
];

function activePage(string $page): string
{
    return basename($_SERVER['PHP_SELF']) === $page ? 'is-active' : '';
}
?>
