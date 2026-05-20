<?php
$pageTitle = 'Orçamentos | ALB APP';
$pageHeading = 'Orçamentos';
$pageEyebrow = 'Proposta comercial';
include __DIR__ . '/includes/layout-start.php';
?>
<section class="grid quote-layout">
    <div class="grid">
        <article class="card card-pad">
            <div class="section-title">
                <div>
                    <h2>Dados do cliente</h2>
                    <p>Informações principais da proposta</p>
                </div>
            </div>
            <form id="quoteForm" class="field-grid">
                <div class="field">
                    <label for="clientName">Cliente</label>
                    <input id="clientName" name="clientName" placeholder="Nome do cliente">
                </div>
                <div class="field">
                    <label for="clientPhone">Telefone</label>
                    <input id="clientPhone" name="clientPhone" placeholder="(00) 00000-0000">
                </div>
                <div class="full-field">
                    <label for="clientAddress">Endereço</label>
                    <input id="clientAddress" name="clientAddress" placeholder="Rua, número, bairro e cidade">
                </div>
                <div class="full-field">
                    <label for="serviceDescription">Descrição do serviço</label>
                    <textarea id="serviceDescription" name="serviceDescription" placeholder="Descreva o escopo do serviço elétrico"></textarea>
                </div>
            </form>
        </article>

        <article class="card card-pad">
            <div class="section-title">
                <div>
                    <h2>Itens do orçamento</h2>
                    <p>Adicione serviços, quantidades e valores</p>
                </div>
                <button class="secondary-button" id="addItemBtn" type="button">Adicionar item</button>
            </div>
            <div class="table-wrap">
                <table class="quote-items-table">
                    <thead>
                        <tr>
                            <th>Serviço</th>
                            <th>Qtd.</th>
                            <th>Valor unit.</th>
                            <th>Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="quoteItems"></tbody>
                </table>
            </div>
        </article>
    </div>

    <aside class="card card-pad summary-panel">
        <div class="section-title">
            <div>
                <h2>Resumo</h2>
                <p>Cálculo automático</p>
            </div>
        </div>
        <div class="summary-row">
            <span>Subtotal</span>
            <strong id="quoteSubtotal">R$ 0,00</strong>
        </div>
        <div class="summary-row">
            <label for="quoteDiscount">Desconto</label>
            <input id="quoteDiscount" type="number" min="0" step="0.01" value="0">
        </div>
        <div class="summary-total">
            <span>Total final</span>
            <strong id="quoteTotal">R$ 0,00</strong>
        </div>
        <button class="primary-button" id="generatePdfBtn" type="button" style="width:100%;">Gerar PDF</button>
        <button class="secondary-button" id="saveQuoteBtn" type="button" style="width:100%; margin-top:10px;">Salvar orçamento</button>
    </aside>
</section>

<section class="print-area" id="printArea">
    <div class="pdf-page">
        <header class="pdf-header">
            <div class="pdf-logo">
                <img id="pdfLogo" src="assets/img/alb-logo-pdf.png" alt="Logo ALB">
            </div>
            <div class="pdf-title">
                <h1>Proposta Comercial</h1>
                <div id="pdfCompanyInfo">São Luis - MA | (98) 98147-3574</div>
                <span id="pdfDate"></span>
            </div>
        </header>

        <div class="pdf-block">
            <h2>Dados do cliente</h2>
            <p><strong>Cliente:</strong> <span id="pdfClientName"></span></p>
            <p><strong>Telefone:</strong> <span id="pdfClientPhone"></span></p>
            <p><strong>Endereço:</strong> <span id="pdfClientAddress"></span></p>
            <p><strong>Serviço:</strong> <span id="pdfDescription"></span></p>
        </div>

        <div class="pdf-block">
            <table class="pdf-table">
                <thead>
                    <tr>
                        <th>Serviço</th>
                        <th>Qtd.</th>
                        <th>Valor unit.</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody id="pdfItems"></tbody>
            </table>
        </div>

        <div class="pdf-total">
            <span>Total da proposta</span><br>
            <strong id="pdfTotal"></strong>
        </div>

        <div class="pdf-block">
            <strong>Observações</strong>
            <p>Valores válidos por 7 dias. Materiais, prazos e condições podem ser ajustados após vistoria técnica.</p>
        </div>

        <div class="pdf-signature">Assinatura do responsável</div>
        <footer class="pdf-footer" id="pdfFooter">ALB APP - Proposta gerada pelo sistema</footer>
    </div>
</section>
<?php include __DIR__ . '/includes/layout-end.php'; ?>
