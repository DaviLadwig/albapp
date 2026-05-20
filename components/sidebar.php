<?php require_once __DIR__ . '/../includes/config.php'; ?>
<aside class="sidebar" id="sidebar">
    <?php include __DIR__ . '/brand.php'; ?>
    <nav class="sidebar-nav" aria-label="Menu principal">
        <?php foreach ($navItems as $item): ?>
            <a class="nav-link <?php echo activePage($item['href']); ?>" href="<?php echo $item['href']; ?>">
                <span class="nav-icon" data-icon="<?php echo $item['icon']; ?>"></span>
                <span><?php echo $item['label']; ?></span>
            </a>
        <?php endforeach; ?>
    </nav>
    <div class="sidebar-card">
        <span>Status</span>
        <strong>Ambiente local</strong>
        <small>Dados temporários via navegador.</small>
    </div>
</aside>
