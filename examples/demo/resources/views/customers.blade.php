<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customers &mdash; CREEM Laravel Demo</title>
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
        .success-box { background: rgba(52,211,153,0.08); border: 1px solid rgba(52,211,153,0.2); border-radius: 12px; padding: 16px 20px; color: var(--green); font-size: 0.88rem; margin-bottom: 24px; }
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
        .empty { padding: 40px 20px; text-align: center; }
        .empty p { color: var(--text-muted); font-size: 0.88rem; line-height: 1.6; }
        .empty a { color: var(--accent); text-decoration: none; }
        .btn-sm { padding: 6px 14px; background: var(--accent); color: #fff; border: none; border-radius: 8px; font-size: 0.75rem; font-weight: 600; cursor: pointer; transition: all 0.2s; text-decoration: none; display: inline-block; }
        .btn-sm:hover { background: #6a4ae8; transform: translateY(-1px); }
        .footer { margin-top: 48px; padding-top: 24px; border-top: 1px solid var(--border); text-align: center; color: var(--text-muted); font-size: 0.78rem; }
        .footer a { color: var(--accent); text-decoration: none; }

        /* Drawer */
        .drawer-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); z-index: 100; opacity: 0; pointer-events: none; transition: opacity 0.3s ease; }
        .drawer-overlay.open { opacity: 1; pointer-events: all; }
        .drawer { position: fixed; top: 0; right: 0; bottom: 0; width: 520px; max-width: 92vw; background: #0c0c14; border-left: 1px solid var(--border-bright); z-index: 101; transform: translateX(100%); transition: transform 0.35s cubic-bezier(0.4,0,0.2,1); display: flex; flex-direction: column; box-shadow: -20px 0 60px rgba(0,0,0,0.5); }
        .drawer.open { transform: translateX(0); }
        .drawer-header { display: flex; justify-content: space-between; align-items: center; padding: 24px 28px; border-bottom: 1px solid var(--border); flex-shrink: 0; }
        .drawer-header h3 { font-size: 1rem; font-weight: 700; }
        .drawer-close { width: 32px; height: 32px; border-radius: 8px; border: 1px solid var(--border); background: var(--surface); color: var(--text-dim); font-size: 1.1rem; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.15s; }
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
            <div class="header-title">Customers</div>
            <div class="header-sub">Fetched via <code>Creem::listCustomers()</code> &middot; Detail via <code>Creem::getCustomer()</code></div>
        </div>

        <div class="nav">
            <a href="/" class="nav-link">&larr; Dashboard</a>
            <a href="/products" class="nav-link">Products</a>
            <a href="/customers" class="nav-active">Customers</a>
            <a href="/subscriptions" class="nav-link">Subscriptions</a>
            <a href="/transactions" class="nav-link">Transactions</a>
            <a href="/licenses" class="nav-link">Licenses</a>
            <a href="/discounts" class="nav-link">Discounts</a>
        </div>

        @if($error ?? false)
            <div class="error-box">{{ $error }}</div>
        @endif

        <div class="card">
            @if(count($customers) > 0)
                <div style="overflow-x:auto;">
                    <table>
                        <thead><tr><th>Customer ID</th><th>Email</th><th>Name</th><th>Created</th><th></th></tr></thead>
                        <tbody>
                            @foreach($customers as $idx => $customer)
                            <tr class="clickable-row" onclick="openCustomerDrawer({{ $idx }})">
                                <td class="mono accent">{{ $customer['id'] ?? '' }}</td>
                                <td class="mono text-dim">{{ $customer['email'] ?? '' }}</td>
                                <td>{{ $customer['name'] ?? 'N/A' }}</td>
                                <td class="text-dim" style="font-size:0.82rem;">{{ $customer['created_at'] ?? '' }}</td>
                                <td><span class="btn-sm" onclick="event.stopPropagation(); openBillingPortal('{{ $customer['id'] ?? '' }}')">Billing Portal</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty">
                    <p>No customers found.<br>Complete a <a href="/products">checkout</a> to create your first customer.</p>
                </div>
            @endif
        </div>

        <div class="footer">
            Built with <a href="https://github.com/Haniamin90/creem-laravel">creem/laravel</a> &middot; <code>listCustomers</code> &middot; <code>getCustomer</code> &middot; <code>customerBillingPortal</code>
        </div>
    </div>

    <div class="drawer-overlay" id="drawerOverlay" onclick="closeDrawer()"></div>
    <div class="drawer" id="drawer">
        <div class="drawer-header">
            <h3 id="drawerTitle"></h3>
            <button class="drawer-close" onclick="closeDrawer()">&times;</button>
        </div>
        <div class="drawer-body" id="drawerBody"></div>
    </div>

    <script>
        const customers = @json(collect($customers)->values());

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

        function openCustomerDrawer(idx) {
            const c = customers[idx];
            if (!c) return;

            let html = '<div class="detail-section"><div class="detail-section-title">Customer Info</div>';
            const fields = [['ID', c.id, 'accent'], ['Email', c.email, 'accent'], ['Name', c.name, ''], ['Created', c.created_at, ''], ['Country', c.country, '']];
            fields.forEach(([k, v, cls]) => {
                if (v) html += `<div class="detail-row"><span class="detail-key">${k}</span><span class="detail-val ${cls}">${esc(safeStr(v))}</span></div>`;
            });
            html += '</div>';

            html += '<div class="detail-section"><div class="detail-section-title">Full Response</div>';
            html += `<div class="json-block">${esc(JSON.stringify(c, null, 2))}</div></div>`;

            // Billing portal section
            html += '<div class="detail-section" id="portal-section"><div class="detail-section-title">Billing Portal</div>';
            html += `<button class="btn-sm" onclick="loadBillingPortal('${esc(c.id || '')}')">Load Billing Portal URL</button></div>`;

            openDrawer('&#128101; Customer Details', html);
        }

        async function loadBillingPortal(customerId) {
            const section = document.getElementById('portal-section');
            if (!section) return;
            section.innerHTML = '<div class="detail-section-title">Billing Portal</div><div class="json-block" style="opacity:0.5;">Loading via Creem::customerBillingPortal()...</div>';
            try {
                const res = await fetch('/api/customer/' + encodeURIComponent(customerId) + '/billing-portal');
                const data = await res.json();
                if (data.portal && data.portal.customer_portal_link) {
                    section.innerHTML = '<div class="detail-section-title">Billing Portal</div><div class="json-block"><a href="' + esc(data.portal.customer_portal_link) + '" target="_blank" style="color:var(--accent);word-break:break-all;">' + esc(data.portal.customer_portal_link) + '</a></div>';
                } else {
                    section.innerHTML = '<div class="detail-section-title">Billing Portal</div><div class="json-block">' + esc(JSON.stringify(data, null, 2)) + '</div>';
                }
            } catch(e) {
                section.innerHTML = '<div class="detail-section-title">Billing Portal</div><div class="json-block">Error: ' + esc(e.message) + '</div>';
            }
        }

        async function openBillingPortal(customerId) {
            try {
                const res = await fetch('/api/customer/' + encodeURIComponent(customerId) + '/billing-portal');
                const data = await res.json();
                if (data.portal && data.portal.customer_portal_link) {
                    window.open(data.portal.customer_portal_link, '_blank');
                } else {
                    alert('Could not get billing portal URL');
                }
            } catch(e) {
                alert('Error: ' + e.message);
            }
        }
    </script>
</body>
</html>
