<?php
$pageTitle = 'Dashboard | ALB APP';
$pageHeading = 'Dashboard';
$pageEyebrow = 'Visão geral';
include __DIR__ . '/includes/layout-start.php';
?>
<section class="grid grid-4">
    <article class="card card-pad metric">
        <span>Total de orçamentos</span>
        <strong id="metricQuotes">0</strong>
        <small>Atualizado pelo navegador</small>
    </article>
    <article class="card card-pad metric">
        <span>Total faturado</span>
        <strong id="metricRevenue">R$ 0,00</strong>
        <small>Com base nos lançamentos</small>
    </article>
    <article class="card card-pad metric">
        <span>Pendentes</span>
        <strong id="metricPending">0</strong>
        <small>Aguardando aprovação</small>
    </article>
    <article class="card card-pad metric">
        <span>Saldo atual</span>
        <strong id="metricBalance">R$ 0,00</strong>
        <small>Entradas menos saídas</small>
    </article>
</section>

<section class="grid grid-2" style="margin-top:18px;">
    <article class="card card-pad">
        <div class="section-title">
            <div>
                <h2>Resumo mensal</h2>
                <p>Gráfico simples de performance</p>
            </div>
        </div>
        <div class="chart" id="dashboardChart"></div>
    </article>
    <article class="card card-pad">
        <div class="section-title">
            <div>
                <h2>Últimos orçamentos</h2>
                <p>Propostas geradas recentemente</p>
            </div>
            <a class="secondary-button" href="orcamentos.php">Novo</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="latestQuotes"></tbody>
            </table>
        </div>
    </article>
</section>
<?php include __DIR__ . '/includes/layout-end.php'; ?>
