<?php
$pageTitle = 'Login | ALB APP';
$bodyClass = 'login-page';
include __DIR__ . '/includes/head.php';
$brandCentered = true;
?>
<section class="login-card">
    <?php include __DIR__ . '/components/brand.php'; ?>
    <h1>Acesse seu painel</h1>
    <p>Gere propostas elétricas, acompanhe valores e mantenha seus dados comerciais organizados.</p>
    <form id="loginForm" class="grid">
        <div class="field">
            <label for="email">Email</label>
            <input id="email" type="email" autocomplete="email" required>
        </div>
        <div class="field">
            <label for="password">Senha</label>
            <input id="password" type="password" autocomplete="current-password" required>
        </div>
        <p class="form-message" id="loginMessage" role="alert"></p>
        <button class="primary-button" type="submit">Entrar</button>
    </form>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
