<?php
$pageTitle = 'Configurações | ALB APP';
$pageHeading = 'Configurações';
$pageEyebrow = 'Empresa';
include __DIR__ . '/includes/layout-start.php';
?>
<section class="card card-pad">
    <div class="section-title">
        <div>
            <h2>Dados da empresa</h2>
            <p>Usados no cabeçalho do PDF</p>
        </div>
    </div>
    <form id="settingsForm" class="field-grid">
        <div class="field">
            <label for="companyName">Nome da empresa</label>
            <input id="companyName" placeholder="ALB APP">
        </div>
        <div class="field">
            <label for="companyPhone">Telefone</label>
            <input id="companyPhone" placeholder="(00) 00000-0000">
        </div>
        <div class="full-field">
            <label for="companyAddress">Endereço</label>
            <input id="companyAddress" placeholder="Endereço comercial">
        </div>
        <div class="field">
            <label for="companyPix">Chave Pix</label>
            <input id="companyPix" placeholder="email, CPF/CNPJ ou chave aleatória">
        </div>
        <div class="field">
            <label for="companyLogo">Upload logo</label>
            <input id="companyLogo" type="file" accept="image/*">
        </div>
        <div class="full-field">
            <button class="primary-button" type="submit">Salvar configurações</button>
        </div>
    </form>
</section>
<?php include __DIR__ . '/includes/layout-end.php'; ?>
