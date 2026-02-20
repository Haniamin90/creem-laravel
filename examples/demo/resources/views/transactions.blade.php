<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transactions &mdash; CREEM Laravel Demo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --bg: #06060b; --surface: rgba(255,255,255,0.03); --surface-hover: rgba(255,255,255,0.06);
            --border: rgba(255,255,255,0.06); --border-bright: rgba(255,255,255,0.12);
            --text: #f0f0f5; --text-dim: #8888a0; --text-muted: #55556a;
            --accent: #7c5cfc; --accent-glow: rgba(124,92,252,0.15);
            --green: #34d399; --amber: #fbbf24; --rose: #fb7185; --blue: #60a5fa;
            --mono: 'JetBrains Mono','SF Mono','Fira Code',monospace;
        }
        body { font-family: 'Inter',-apple-system,sans-serif; background: var(--bg); color: var(--text); min-height: 100vh; -webkit-font-smoothing: antialiased; }
        body::before { content:''; position: fixed; top:-40%; left:-20%; width:80%; height:80%; background: radial-gradient(ellipse, rgba(124,92,252,0.06) 0%, transparent 70%); pointer-events: none; }
        body::after { content:''; position: fixed; bottom:-30%; right:-10%; width:60%; height:60%; background: radial-gradient(ellipse, rgba(251,113,133,0.04) 0%, transparent 70%); pointer-events: none; }
        .container { max-width: 1080px; margin: 0 auto; padding: 48px 24px; position: relative; z-index: 1; }
        .header { margin-bottom: 40px; }
        .header-title { font-size: 1.75rem; font-weight: 800; letter-spacing: -0.03em; background: linear-gradient(135deg, #fff 0%, #aaa 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .header-sub { font-size: 0.95rem; color: var(--text-dim); margin-top: 4px; }
        .header-sub code { font-family: var(--mono); color: var(--accent); font-size: 0.85rem; background: var(--accent-glow); padding: 2px 8px; border-radius: 4px; }
        .nav { display: flex; gap: 8px; margin-bottom: 36px; flex-wrap: wrap; }
        .nav a { padding: 9px 20px; border-radius: 10px; font-size: 0.82rem; font-weight: 500; text-decoration: none; transition: all 0.2s; }
        .nav-active { background: var(--accent); color: #fff; box-shadow: 0 4px 20px var(--accent-glow); }
        .nav-link { background: var(--surface); color: var(--text-dim); border: 1px solid var(--border); }
        .nav-link:hover { background: var(--surface-hover); color: var(--text); border-color: var(--border-bright); }
        .error-box { background: rgba(251,113,133,0.08); border: 1px solid rgba(251,113,133,0.2); border-radius: 12px; padding: 16px 20px; color: var(--rose); font-size: 0.88rem; margin-bottom: 24px; }
        .card { background: var(--surface); backdrop-filter: blur(20px); border: 1px solid var(--border); border-radius: 16px; padding: 28px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 10px 14px; color: var(--text-muted); font-weight: 500; font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.08em; border-bottom: 1px solid var(--border); }
        td { padding: 12px 14px; border-bottom: 1px solid rgba(255,255,255,0.02); font-size: 0.85rem; }
        .clickable-row { cursor: pointer; transition: background 0.15s; }
        .clickable-row:hover td { background: rgba(124,92,252,0.04); }
        .mono { font-family: var(--mono); font-size: 0.78rem; }
        .mono.accent { color: var(--accent); }
        .text-dim { color: var(--text-dim); }
        .text-muted { color: var(--text-muted); }
        .amount { font-weight: 700; color: var(--green); }
        .empty { padding: 40px 20px; text-align: center; }
        .empty p { color: var(--text-muted); font-size: 0.88rem; line-height: 1.6; }
        .empty a { color: var(--accent); text-decoration: none; }
        .status { display: inline-block; padding: 2px 10px; border-radius: 6px; font-size: 0.72rem; font-weight: 600; text-transform: uppercase; }
        .status-succeeded { background: rgba(52,211,153,0.1); color: var(--green); }
        .status-pending { background: rgba(251,191,36,0.1); color: var(--amber); }
        .status-failed { background: rgba(251,113,133,0.1); color: var(--rose); }
        .footer { margin-top: 48px; padding-top: 24px; border-top: 1px solid var(--border); text-align: center; color: var(--text-muted); font-size: 0.78rem; }
        .footer a { color: var(--accent); text-decoration: none; }

        /* Drawer */
        .drawer-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); z-index: 100; opacity: 0; pointer-events: none; transition: opacity 0.3s ease; }
        .drawer-overlay.open { opacity: 1; pointer-events: all; }
        .drawer { position: fixed; top: 0; right: 0; bottom: 0; width: 520px; max-width: 92vw; background: #0c0c14; border-left: 1px solid var(--border-bright); z-index: 101; transform: translateX(100%); transition: transform 0.35s cubic-bezier(0.4,0,0.2,1); display: flex; flex-direction: column; box-shadow: -20px 0 60px rgba(0,0,0,0.5); }
        .drawer.open { transform: translateX(0); }
        .drawer-header { display: flex; justify-content: space-between; align-items: center; padding: 24px 28px; border-bottom: 1px solid var(--border); flex-shrink: 0; }
        .drawer-header h3 { font-size: 1rem; font-weight: 700; }
        .drawer-close { width: 32px; height: 32px; border-radius: 8px; border: 1px solid var(--border); background: var(--surface); color: var(--text-dim); font-size: 1.1rem; cursor: pointer; display: flex; align-items: center; justify-content: center; }
        .drawer-close:hover { background: var(--surface-hover); color: var(--text); }
        .drawer-body { flex: 1; overflow-y: auto; padding: 24px 28px; }
        .detail-section { margin-bottom: 24px; }
        .detail-section-title { font-size: 0.72rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em; color: var(--text-muted); margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px solid var(--border); }
        .detail-row { display: flex; justify-content: space-between; align-items: flex-start; padding: 8px 0; gap: 16px; }
        .detail-row + .detail-row { border-top: 1px solid rgba(255,255,255,0.02); }
        .detail-key { color: var(--text-muted); font-size: 0.82rem; font-weight: 500; flex-shrink: 0; min-width: 120px; }
        .detail-val { font-family: var(--mono); font-size: 0.78rem; color: var(--text-dim); text-align: right; word-break: break-all; }
        .detail-val.accent { color: var(--accent); }
        .detail-val.green { color: var(--green); }
        .json-block { background: rgba(0,0,0,0.4); border: 1px solid var(--border); border-radius: 10px; padding: 16px; font-family: var(--mono); font-size: 0.72rem; color: var(--text-dim); line-height: 1.7; overflow-x: auto; white-space: pre-wrap; word-break: break-all; max-height: 400px; overflow-y: auto; }
        @media (max-width: 640px) { .container { padding: 24px 16px; } .drawer { width: 100%; max-width: 100vw; } }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-title">Transactions</div>
            <div class="header-sub">Fetched via <code>Creem::searchTransactions()</code> &middot; Detail via <code>Creem::getTransaction()</code></div>
        </div>

        <div class="nav">
            <a href="/" class="nav-link">&larr; Dashboard</a>
            <a href="/products" class="nav-link">Products</a>
            <a href="/customers" class="nav-link">Customers</a>
            <a href="/subscriptions" class="nav-link">Subscriptions</a>
            <a href="/transactions" class="nav-active">Transactions</a>
            <a href="/licenses" class="nav-link">Licenses</a>
            <a href="/discounts" class="nav-link">Discounts</a>
        </div>

        @if($error ?? false)
            <div class="error-box">{{ $error }}</div>
        @endif

        <div class="card">
            @if(count($transactions) > 0)
                <div style="overflow-x:auto;">
                    <table>
                        <thead><tr><th>Transaction ID</th><th>Status</th><th>Amount</th><th>Customer</th><th>Created</th></tr></thead>
                        <tbody>
                            @foreach($transactions as $idx => $txn)
                            @php
                                $status = $txn['status'] ?? 'unknown';
                                $statusCls = match($status) {
                                    'succeeded' => 'status-succeeded',
                                    'pending' => 'status-pending',
                                    'failed' => 'status-failed',
                                    default => ''
                                };
                                $amount = ($txn['amount'] ?? 0) / 100;
                                $currency = strtoupper($txn['currency'] ?? 'USD');
                            @endphp
                            <tr class="clickable-row" onclick="openTxnDrawer({{ $idx }})">
                                <td class="mono accent">{{ Str::limit($txn['id'] ?? '', 24) }}</td>
                                <td><span class="status {{ $statusCls }}">{{ $status }}</span></td>
                                <td class="amount">${{ number_format($amount, 2) }} <span class="text-muted" style="font-size:0.72rem;">{{ $currency }}</span></td>
                                <td class="mono text-dim" style="font-size:0.75rem;">{{ Str::limit($txn['customer_id'] ?? ($txn['customer']['id'] ?? ''), 20) }}</td>
                                <td class="text-dim" style="font-size:0.82rem;">{{ $txn['created_at'] ?? '' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty">
                    <p>No transactions found.<br>Complete a <a href="/products">checkout</a> to create your first transaction.</p>
                </div>
            @endif
        </div>

        <div class="footer">
            Built with <a href="https://github.com/Haniamin90/creem-laravel">creem/laravel</a> &middot; <code>searchTransactions</code> &middot; <code>getTransaction</code>
        </div>
    </div>

    <div class="drawer-overlay" id="drawerOverlay" onclick="closeDrawer()"></div>
    <div class="drawer" id="drawer">
        <div class="drawer-header"><h3 id="drawerTitle"></h3><button class="drawer-close" onclick="closeDrawer()">&times;</button></div>
        <div class="drawer-body" id="drawerBody"></div>
    </div>

    <script>
        const transactions = @json(collect($transactions)->values());

        function openDrawer(title, html) {
            document.getElementById('drawerTitle').innerHTML = title;
            document.getElementById('drawerBody').innerHTML = html;
            document.getElementById('drawerOverlay').classList.add('open');
            document.getElementById('drawer').classList.add('open');
            document.body.style.overflow = 'hidden';
        }
        function closeDrawer() {
            document.getElementById('drawerOverlay').classList.remove('open');
            document.getElementById('drawer').classList.remove('open');
            document.body.style.overflow = '';
        }
        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeDrawer(); });

        function esc(s) { const d = document.createElement('div'); d.textContent = s; return d.innerHTML; }
        function safeStr(v) { if (v === null || v === undefined) return ''; if (typeof v === 'object') return JSON.stringify(v); return String(v); }

        function openTxnDrawer(idx) {
            const t = transactions[idx];
            if (!t) return;

            const amount = (t.amount || 0) / 100;
            const status = t.status || 'unknown';

            let html = '<div class="detail-section"><div class="detail-section-title">Transaction Info</div>';
            html += row('ID', t.id, 'accent');
            html += row('Status', status);
            html += row('Amount', '$' + amount.toFixed(2) + ' ' + (t.currency || 'USD').toUpperCase(), 'green');
            if (t.customer_id || t.customer?.id) html += row('Customer ID', safeStr(t.customer?.id || t.customer_id), 'accent');
            if (t.product_id || t.product?.id) html += row('Product ID', safeStr(t.product?.id || t.product_id), 'accent');
            if (t.subscription_id) html += row('Subscription', t.subscription_id, 'accent');
            if (t.created_at) html += row('Created', t.created_at);
            html += '</div>';

            html += '<div class="detail-section"><div class="detail-section-title">Full Response</div>';
            html += `<div class="json-block">${esc(JSON.stringify(t, null, 2))}</div></div>`;

            openDrawer('&#128176; Transaction Details', html);
        }

        function row(label, value, cls) {
            return `<div class="detail-row"><span class="detail-key">${label}</span><span class="detail-val ${cls || ''}">${esc(safeStr(value))}</span></div>`;
        }
    </script>
</body>
</html>
