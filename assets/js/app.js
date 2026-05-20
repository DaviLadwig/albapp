const BRL = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' });

const store = {
    get(key, fallback) {
        const value = localStorage.getItem(key);
        return value ? JSON.parse(value) : fallback;
    },
    set(key, value) {
        localStorage.setItem(key, JSON.stringify(value));
    }
};

const api = {
    enabled: false,
    token() {
        return localStorage.getItem('albAccessToken') || '';
    },
    logout() {
        fetch('api/logout.php', { method: 'POST' }).catch(() => {});
        localStorage.removeItem('albAccessToken');
        localStorage.removeItem('albUserEmail');
        window.location.href = 'index.php';
    },
    async request(url, options = {}) {
        const headers = { 'Content-Type': 'application/json', ...(options.headers || {}) };
        if (this.token()) {
            headers.Authorization = `Bearer ${this.token()}`;
        }

        const response = await fetch(url, {
            ...options,
            headers
        });

        if (response.status === 401) {
            this.logout();
            throw new Error('Login necessário.');
        }

        if (!response.ok) {
            const detail = await response.json().catch(() => ({}));
            const error = new Error(detail.error || detail.msg || `API indisponível: ${response.status}`);
            error.status = response.status;
            throw error;
        }

        return response.json();
    },
    async sync() {
        if (!this.token()) {
            this.enabled = false;
            return;
        }

        try {
            const [settings, finance, quotes] = await Promise.all([
                this.request('api/settings.php'),
                this.request('api/finance.php'),
                this.request('api/quotes.php')
            ]);

            if (settings[0]) {
                store.set('albSettings', {
                    companyName: settings[0].company_name || 'ALB APP',
                    companyPhone: settings[0].company_phone || '',
                    companyAddress: settings[0].company_address || '',
                    companyPix: settings[0].company_pix || '',
                    companyLogo: settings[0].company_logo || 'assets/img/alb-logo.png'
                });
            }

            store.set('albFinance', finance.map(item => ({
                id: item.id,
                date: item.entry_date,
                desc: item.description,
                type: item.type,
                value: Number(item.value || 0)
            })));

            store.set('albQuotes', quotes.map(item => ({
                id: item.id,
                client: item.client_name,
                total: Number(item.total || 0),
                status: item.status,
                createdAt: item.created_at
            })));

            this.enabled = true;
        } catch (error) {
            this.enabled = false;
        }
    }
};

const defaults = {
    settings: {
        companyName: 'ALB APP',
        companyPhone: '(98) 98147-3574',
        companyAddress: 'São Luis - MA',
        companyPix: '(98) 98147-3574',
        companyLogo: 'assets/img/alb-logo.png'
    },
    quoteItems: [
        { service: 'Instalação de ponto elétrico', qty: 4, price: 120 },
        { service: 'Revisão de quadro de distribuição', qty: 1, price: 380 }
    ],
    finance: [
        { id: crypto.randomUUID(), date: '2026-05-02', desc: 'Serviço residencial', type: 'entrada', value: 1250 },
        { id: crypto.randomUUID(), date: '2026-05-04', desc: 'Compra de disjuntores', type: 'saida', value: 220 },
        { id: crypto.randomUUID(), date: '2026-05-08', desc: 'Manutenção comercial', type: 'entrada', value: 1850 }
    ]
};

function seedData() {
    if (!localStorage.getItem('albSettings')) store.set('albSettings', defaults.settings);
    const currentSettings = store.get('albSettings', defaults.settings);
    if (currentSettings.companyAddress === 'Fortaleza, CE' || currentSettings.companyPhone === '(85) 99999-0000') {
        store.set('albSettings', {
            ...currentSettings,
            companyPhone: '(98) 98147-3574',
            companyAddress: 'São Luis - MA',
            companyPix: '(98) 98147-3574'
        });
    }
    if (!localStorage.getItem('albFinance')) store.set('albFinance', defaults.finance);
    if (!localStorage.getItem('albQuotes')) {
        store.set('albQuotes', [
            { id: crypto.randomUUID(), client: 'Residencial Lagoa', total: 1420, status: 'Pendente', createdAt: '2026-05-10' },
            { id: crypto.randomUUID(), client: 'Mercado Central', total: 2650, status: 'Aprovado', createdAt: '2026-05-12' }
        ]);
    }
}

function qs(selector) {
    return document.querySelector(selector);
}

function qsa(selector) {
    return [...document.querySelectorAll(selector)];
}

function numberFrom(input) {
    return Number(input.value || 0);
}

function escapeHtml(value) {
    return String(value)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}

function initShell() {
    const toggle = qs('#sidebarToggle');
    const sidebar = qs('#sidebar');
    const backdrop = qs('#mobileBackdrop');

    if (!toggle || !sidebar || !backdrop) return;

    const close = () => {
        sidebar.classList.remove('is-open');
        backdrop.classList.remove('is-open');
    };

    toggle.addEventListener('click', () => {
        sidebar.classList.toggle('is-open');
        backdrop.classList.toggle('is-open');
    });

    backdrop.addEventListener('click', close);

    const logout = qs('#logoutButton');
    if (logout) {
        logout.addEventListener('click', (event) => {
            event.preventDefault();
            api.logout();
        });
    }
}

function initAuthGuard() {
    const isLogin = document.body.classList.contains('login-page');
    if (!isLogin && !api.token()) {
        window.location.href = 'index.php';
    }
}

function initLogin() {
    const form = qs('#loginForm');
    if (!form) return;
    const message = qs('#loginMessage');

    if (api.token()) {
        window.location.href = 'dashboard.php';
        return;
    }

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        message.textContent = '';
        const button = form.querySelector('button');
        button.disabled = true;
        button.textContent = 'Entrando...';

        try {
            const data = await api.request('api/login.php', {
                method: 'POST',
                body: JSON.stringify({
                    email: qs('#email').value,
                    password: qs('#password').value
                })
            });

            if (!data.access_token) {
                throw new Error(data.error_description || data.msg || data.error || 'Não foi possível entrar.');
            }

            localStorage.setItem('albAccessToken', data.access_token);
            localStorage.setItem('albUserEmail', data.user?.email || qs('#email').value);
            window.location.href = 'dashboard.php';
        } catch (error) {
            message.textContent = error.status === 503
                ? 'Supabase ainda não está configurado. Preencha a SUPABASE_ANON_KEY no arquivo .env.'
                : 'Email ou senha inválidos. Verifique seu usuário no Supabase.';
        } finally {
            button.disabled = false;
            button.textContent = 'Entrar';
        }
    });
}

async function initDashboard() {
    if (!qs('#metricQuotes')) return;

    const quotes = store.get('albQuotes', []);
    const finance = store.get('albFinance', []);
    const income = finance.filter(item => item.type === 'entrada').reduce((sum, item) => sum + item.value, 0);
    const expense = finance.filter(item => item.type === 'saida').reduce((sum, item) => sum + item.value, 0);
    const balance = income - expense;
    const pending = quotes.filter(item => item.status === 'Pendente').length;

    qs('#metricQuotes').textContent = quotes.length;
    qs('#metricRevenue').textContent = BRL.format(income);
    qs('#metricPending').textContent = pending;
    qs('#metricBalance').textContent = BRL.format(balance);

    const latest = qs('#latestQuotes');
    latest.innerHTML = quotes.slice(-5).reverse().map(quote => `
        <tr>
            <td>${escapeHtml(quote.client)}</td>
            <td>${BRL.format(quote.total)}</td>
            <td><span class="status-pill">${escapeHtml(quote.status)}</span></td>
        </tr>
    `).join('');

    const chart = qs('#dashboardChart');
    const values = [35, 58, 42, 76, 64, 88];
    chart.innerHTML = values.map(value => `<span class="bar" style="height:${value}%"></span>`).join('');
}

function quoteRows() {
    return qsa('#quoteItems tr').map(row => ({
        service: row.querySelector('[data-field="service"]').value,
        qty: Number(row.querySelector('[data-field="qty"]').value || 0),
        price: Number(row.querySelector('[data-field="price"]').value || 0)
    }));
}

function renderQuoteItem(item = { service: '', qty: 1, price: 0 }) {
    const row = document.createElement('tr');
    row.innerHTML = `
        <td><input data-field="service" value="${escapeHtml(item.service)}" placeholder="Serviço"></td>
        <td><input data-field="qty" type="number" min="0" step="1" value="${item.qty}"></td>
        <td><input data-field="price" type="number" min="0" step="0.01" value="${item.price}"></td>
        <td data-subtotal>${BRL.format(item.qty * item.price)}</td>
        <td><button class="danger-button" type="button" aria-label="Remover item">×</button></td>
    `;
    row.querySelectorAll('input').forEach(input => input.addEventListener('input', updateQuoteTotals));
    row.querySelector('button').addEventListener('click', () => {
        row.remove();
        updateQuoteTotals();
    });
    qs('#quoteItems').appendChild(row);
}

function updateQuoteTotals() {
    const items = quoteRows();
    const subtotal = items.reduce((sum, item) => sum + item.qty * item.price, 0);
    const discount = numberFrom(qs('#quoteDiscount'));
    const total = Math.max(subtotal - discount, 0);

    qsa('#quoteItems tr').forEach((row, index) => {
        const item = items[index];
        row.querySelector('[data-subtotal]').textContent = BRL.format(item.qty * item.price);
    });

    qs('#quoteSubtotal').textContent = BRL.format(subtotal);
    qs('#quoteTotal').textContent = BRL.format(total);
    return { subtotal, discount, total, items };
}

function buildPdf() {
    const settings = store.get('albSettings', defaults.settings);
    const quote = updateQuoteTotals();
    const today = new Date().toLocaleDateString('pt-BR');

    qs('#pdfCompanyInfo').textContent = `${settings.companyAddress || 'São Luis - MA'} | ${settings.companyPhone || '(98) 98147-3574'}`;
    qs('#pdfLogo').src = 'assets/img/alb-logo-pdf.png';
    qs('#pdfDate').textContent = today;
    qs('#pdfClientName').textContent = qs('#clientName').value || 'Cliente não informado';
    qs('#pdfClientPhone').textContent = qs('#clientPhone').value || '-';
    qs('#pdfClientAddress').textContent = qs('#clientAddress').value || '-';
    qs('#pdfDescription').textContent = qs('#serviceDescription').value || '-';
    qs('#pdfItems').innerHTML = quote.items.map(item => `
        <tr>
            <td>${escapeHtml(item.service || '-')}</td>
            <td>${item.qty}</td>
            <td>${BRL.format(item.price)}</td>
            <td>${BRL.format(item.qty * item.price)}</td>
        </tr>
    `).join('');
    qs('#pdfTotal').textContent = BRL.format(quote.total);
    qs('#pdfFooter').textContent = `ALB Elétrica & Automação${settings.companyPix ? ' - Pix: ' + settings.companyPix : ' - Proposta gerada pelo sistema'}`;
}

async function saveCurrentQuote(status = 'Pendente') {
    const quote = updateQuoteTotals();
    const payload = {
        clientName: qs('#clientName').value || 'Cliente sem nome',
        clientPhone: qs('#clientPhone').value || '',
        clientAddress: qs('#clientAddress').value || '',
        serviceDescription: qs('#serviceDescription').value || '',
        subtotal: quote.subtotal,
        discount: quote.discount,
        total: quote.total,
        status,
        items: quote.items
    };

    if (api.enabled) {
        try {
            await api.request('api/quotes.php', {
                method: 'POST',
                body: JSON.stringify(payload)
            });
            await api.sync();
            return true;
        } catch (error) {
            api.enabled = false;
        }
    }

    const quotes = store.get('albQuotes', []);
    quotes.push({
        id: crypto.randomUUID(),
        client: payload.clientName,
        total: quote.total,
        status,
        createdAt: new Date().toISOString().slice(0, 10)
    });
    store.set('albQuotes', quotes);
    return false;
}

function initQuotes() {
    if (!qs('#quoteItems')) return;

    defaults.quoteItems.forEach(renderQuoteItem);
    qs('#addItemBtn').addEventListener('click', () => {
        renderQuoteItem();
        updateQuoteTotals();
    });
    qs('#quoteDiscount').addEventListener('input', updateQuoteTotals);
    qs('#saveQuoteBtn').addEventListener('click', async () => {
        const savedRemote = await saveCurrentQuote();
        alert(savedRemote ? 'Orçamento salvo no Supabase.' : 'Orçamento salvo no navegador.');
    });
    qs('#generatePdfBtn').addEventListener('click', async () => {
        await saveCurrentQuote();
        buildPdf();
        window.print();
    });
    updateQuoteTotals();
}

function renderFinance() {
    const finance = store.get('albFinance', []);
    const income = finance.filter(item => item.type === 'entrada').reduce((sum, item) => sum + item.value, 0);
    const expense = finance.filter(item => item.type === 'saida').reduce((sum, item) => sum + item.value, 0);

    qs('#financeIncome').textContent = BRL.format(income);
    qs('#financeExpense').textContent = BRL.format(expense);
    qs('#financeBalance').textContent = BRL.format(income - expense);
    qs('#financeCount').textContent = finance.length;

    qs('#financeTable').innerHTML = finance.slice().reverse().map(item => `
        <tr>
            <td>${new Date(item.date + 'T00:00:00').toLocaleDateString('pt-BR')}</td>
            <td>${escapeHtml(item.desc)}</td>
            <td class="${item.type === 'entrada' ? 'finance-type-in' : 'finance-type-out'}">${item.type}</td>
            <td>${BRL.format(item.value)}</td>
            <td><button class="danger-button" type="button" data-remove-finance="${item.id}">×</button></td>
        </tr>
    `).join('');

    qsa('[data-remove-finance]').forEach(button => {
        button.addEventListener('click', () => {
            const updated = finance.filter(item => item.id !== button.dataset.removeFinance);
            store.set('albFinance', updated);
            renderFinance();
            if (api.enabled) {
                api.request(`api/finance.php?id=${encodeURIComponent(button.dataset.removeFinance)}`, {
                    method: 'DELETE'
                }).catch(() => {
                    api.enabled = false;
                });
            }
        });
    });
}

function initFinance() {
    const form = qs('#financeForm');
    if (!form) return;

    renderFinance();
    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        const payload = {
            id: crypto.randomUUID(),
            date: new Date().toISOString().slice(0, 10),
            desc: qs('#financeDesc').value || 'Lançamento',
            type: qs('#financeType').value,
            value: Number(qs('#financeValue').value || 0)
        };

        if (api.enabled) {
            try {
                await api.request('api/finance.php', {
                    method: 'POST',
                    body: JSON.stringify(payload)
                });
                await api.sync();
                renderFinance();
                form.reset();
                return;
            } catch (error) {
                api.enabled = false;
            }
        }

        const finance = store.get('albFinance', []);
        finance.push(payload);
        store.set('albFinance', finance);
        form.reset();
        renderFinance();
    });
}

function initSettings() {
    const form = qs('#settingsForm');
    if (!form) return;

    const settings = store.get('albSettings', defaults.settings);
    qs('#companyName').value = settings.companyName || '';
    qs('#companyPhone').value = settings.companyPhone || '';
    qs('#companyAddress').value = settings.companyAddress || '';
    qs('#companyPix').value = settings.companyPix || '';

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        const file = qs('#companyLogo').files[0];
        const save = async (logo = settings.companyLogo || '') => {
            const payload = {
                companyName: qs('#companyName').value,
                companyPhone: qs('#companyPhone').value,
                companyAddress: qs('#companyAddress').value,
                companyPix: qs('#companyPix').value,
                companyLogo: logo
            };

            if (api.enabled) {
                try {
                    await api.request('api/settings.php', {
                        method: 'POST',
                        body: JSON.stringify(payload)
                    });
                    await api.sync();
                    alert('Configurações salvas no Supabase.');
                    return;
                } catch (error) {
                    api.enabled = false;
                }
            }

            store.set('albSettings', payload);
            alert('Configurações salvas no navegador.');
        };

        if (!file) {
            save();
            return;
        }

        const reader = new FileReader();
        reader.onload = () => save(reader.result);
        reader.readAsDataURL(file);
    });
}

document.addEventListener('DOMContentLoaded', async () => {
    seedData();
    initAuthGuard();
    await api.sync();
    initShell();
    initLogin();
    initDashboard();
    initQuotes();
    initFinance();
    initSettings();

    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('sw.js').catch(() => {});
    }
});
