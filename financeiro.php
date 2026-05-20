<?php
$pageTitle = 'Financeiro | ALB APP';
$pageHeading = 'Financeiro';
$pageEyebrow = 'Controle básico';
include __DIR__ . '/includes/layout-start.php';
?>
<section class="grid grid-4">
    <article class="card card-pad metric">
        <span>Entradas</span>
        <strong id="financeIncome">R$ 0,00</strong>
        <small>Receitas registradas</small>
    </article>
    <article class="card card-pad metric">
        <span>Saídas</span>
        <strong id="financeExpense">R$ 0,00</strong>
        <small>Custos e despesas</small>
    </article>
    <article class="card card-pad metric">
        <span>Saldo</span>
        <strong id="financeBalance">R$ 0,00</strong>
        <small>Resultado atual</small>
    </article>
    <article class="card card-pad metric">
        <span>Lançamentos</span>
        <strong id="financeCount">0</strong>
        <small>Dados mockados editáveis</small>
    </article>
</section>

<section class="card card-pad" style="margin-top:18px;">
    <div class="section-title">
        <div>
            <h2>Novo lançamento</h2>
            <p>Controle simples para fase inicial</p>
        </div>
    </div>
    <form id="financeForm" class="field-grid">
        <div class="field">
            <label for="financeDesc">Descrição</label>
            <input id="financeDesc" placeholder="Ex.: Serviço residencial">
        </div>
        <div class="field">
            <label for="financeValue">Valor</label>
            <input id="financeValue" type="number" step="0.01" min="0" placeholder="0,00">
        </div>
        <div class="field">
            <label for="financeType">Tipo</label>
            <select id="financeType">
                <option value="entrada">Entrada</option>
                <option value="saida">Saída</option>
            </select>
        </div>
        <div class="field">
            <label>&nbsp;</label>
            <button class="primary-button" type="submit">Adicionar</button>
        </div>
    </form>
</section>

<section class="card card-pad" style="margin-top:18px;">
    <div class="section-title">
        <div>
            <h2>Tabela financeira</h2>
            <p>Entradas e saídas salvas localmente</p>
        </div>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Descrição</th>
                    <th>Tipo</th>
                    <th>Valor</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="financeTable"></tbody>
        </table>
    </div>
</section>
<?php include __DIR__ . '/includes/layout-end.php'; ?>
