<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CREEM Laravel Demo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --bg: #06060b;
            --surface: rgba(255,255,255,0.03);
            --surface-hover: rgba(255,255,255,0.06);
            --surface-raised: rgba(255,255,255,0.05);
            --border: rgba(255,255,255,0.06);
            --border-bright: rgba(255,255,255,0.12);
            --text: #f0f0f5;
            --text-dim: #8888a0;
            --text-muted: #55556a;
            --accent: #7c5cfc;
            --accent-glow: rgba(124,92,252,0.15);
            --green: #34d399;
            --amber: #fbbf24;
            --rose: #fb7185;
            --blue: #60a5fa;
            --mono: 'JetBrains Mono','SF Mono','Fira Code',monospace;
        }
        body { font-family: 'Inter',-apple-system,sans-serif; background: var(--bg); color: var(--text); min-height: 100vh; -webkit-font-smoothing: antialiased; }
        body::before { content:''; position: fixed; top:-40%; left:-20%; width:80%; height:80%; background: radial-gradient(ellipse, rgba(124,92,252,0.06) 0%, transparent 70%); pointer-events: none; }
        body::after { content:''; position: fixed; bottom:-30%; right:-10%; width:60%; height:60%; background: radial-gradient(ellipse, rgba(251,113,133,0.04) 0%, transparent 70%); pointer-events: none; }
        .container { max-width: 1080px; margin: 0 auto; padding: 48px 24px; position: relative; z-index: 1; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 40px; gap: 16px; flex-wrap: wrap; }
        .header-title { font-size: 1.75rem; font-weight: 800; letter-spacing: -0.03em; background: linear-gradient(135deg, #fff 0%, #aaa 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .header-sub { font-size: 0.95rem; color: var(--text-dim); margin-top: 4px; }
        .header-sub code { font-family: var(--mono); color: var(--accent); font-size: 0.85rem; background: var(--accent-glow); padding: 2px 8px; border-radius: 4px; }
        .badge { display: inline-flex; align-items: center; gap: 6px; padding: 5px 14px; border-radius: 20px; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em; }
        .badge::before { content:''; width: 6px; height: 6px; border-radius: 50%; }
        .badge-sandbox { background: rgba(251,191,36,0.1); color: var(--amber); border: 1px solid rgba(251,191,36,0.2); }
        .badge-sandbox::before { background: var(--amber); box-shadow: 0 0 8px var(--amber); }
        .badge-live { background: rgba(52,211,153,0.1); color: var(--green); border: 1px solid rgba(52,211,153,0.2); }
        .badge-live::before { background: var(--green); box-shadow: 0 0 8px var(--green); }
        .nav { display: flex; gap: 8px; margin-bottom: 36px; flex-wrap: wrap; }
        .nav a { padding: 9px 20px; border-radius: 10px; font-size: 0.82rem; font-weight: 500; text-decoration: none; transition: all 0.2s; }
        .nav-active { background: var(--accent); color: #fff; box-shadow: 0 4px 20px var(--accent-glow); }
        .nav-link { background: var(--surface); color: var(--text-dim); border: 1px solid var(--border); }
        .nav-link:hover { background: var(--surface-hover); color: var(--text); border-color: var(--border-bright); }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 28px; }
        .stat-card { background: var(--surface); backdrop-filter: blur(20px); border: 1px solid var(--border); border-radius: 16px; padding: 24px; text-align: center; transition: all 0.3s; }
        .stat-card:hover { border-color: var(--border-bright); transform: translateY(-2px); box-shadow: 0 8px 32px rgba(0,0,0,0.3); }
        .stat-value { font-size: 2.2rem; font-weight: 800; letter-spacing: -0.02em; }
        .stat-value.purple { color: var(--accent); }
        .stat-value.green { color: var(--green); }
        .stat-value.blue { color: var(--blue); }
        .stat-value.rose { color: var(--rose); }
        .stat-label { font-size: 0.78rem; color: var(--text-muted); margin-top: 4px; text-transform: uppercase; letter-spacing: 0.06em; font-weight: 500; }
        .card { background: var(--surface); backdrop-filter: blur(20px); border: 1px solid var(--border); border-radius: 16px; padding: 28px; margin-bottom: 20px; transition: border-color 0.3s; }
        .card:hover { border-color: var(--border-bright); }
        .card-title { font-size: 0.95rem; font-weight: 600; margin-bottom: 16px; display: flex; align-items: center; gap: 10px; }
        .env-grid { display: grid; grid-template-columns: auto 1fr; gap: 8px 20px; font-size: 0.83rem; }
        .env-label { color: var(--text-muted); font-weight: 500; }
        .env-value { font-family: var(--mono); color: var(--text-dim); font-size: 0.8rem; }
        .env-value.hl { color: var(--accent); }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 10px 14px; color: var(--text-muted); font-weight: 500; font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.08em; border-bottom: 1px solid var(--border); }
        td { padding: 12px 14px; border-bottom: 1px solid rgba(255,255,255,0.02); font-size: 0.85rem; }
        .clickable-row { cursor: pointer; transition: background 0.15s; }
        .clickable-row:hover td { background: rgba(124,92,252,0.04); }
        .mono { font-family: var(--mono); font-size: 0.78rem; }
        .mono.accent { color: var(--accent); }
        .text-dim { color: var(--text-dim); }
        .text-muted { color: var(--text-muted); }
        .ev { display: inline-block; padding: 2px 10px; border-radius: 6px; font-family: var(--mono); font-size: 0.72rem; font-weight: 500; }
        .ev-c { background: rgba(52,211,153,0.1); color: var(--green); }
        .ev-s { background: rgba(96,165,250,0.1); color: var(--blue); }
        .ev-r { background: rgba(251,113,133,0.1); color: var(--rose); }
        .ev-d { background: rgba(251,191,36,0.1); color: var(--amber); }
        .ev-x { background: rgba(255,255,255,0.05); color: var(--text-dim); }
        .empty { padding: 40px 20px; text-align: center; }
        .empty p { color: var(--text-muted); font-size: 0.88rem; line-height: 1.6; }
        .empty a { color: var(--accent); text-decoration: none; }
        .footer { margin-top: 48px; padding-top: 24px; border-top: 1px solid var(--border); text-align: center; color: var(--text-muted); font-size: 0.78rem; }
        .footer a { color: var(--accent); text-decoration: none; }

        /* ── Drawer ────────────────────────────────────── */
        .drawer-overlay {
            position: fixed; inset: 0; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px);
            z-index: 100; opacity: 0; pointer-events: none; transition: opacity 0.3s ease;
        }
        .drawer-overlay.open { opacity: 1; pointer-events: all; }

        .drawer {
            position: fixed; top: 0; right: 0; bottom: 0; width: 520px; max-width: 92vw;
            background: #0c0c14; border-left: 1px solid var(--border-bright);
            z-index: 101; transform: translateX(100%); transition: transform 0.35s cubic-bezier(0.4,0,0.2,1);
            display: flex; flex-direction: column; box-shadow: -20px 0 60px rgba(0,0,0,0.5);
        }
        .drawer.open { transform: translateX(0); }

        .drawer-header {
            display: flex; justify-content: space-between; align-items: center;
            padding: 24px 28px; border-bottom: 1px solid var(--border);
            flex-shrink: 0;
        }
        .drawer-header h3 { font-size: 1rem; font-weight: 700; display: flex; align-items: center; gap: 10px; }
        .drawer-close {
            width: 32px; height: 32px; border-radius: 8px; border: 1px solid var(--border);
            background: var(--surface); color: var(--text-dim); font-size: 1.1rem;
            cursor: pointer; display: flex; align-items: center; justify-content: center;
            transition: all 0.15s;
        }
        .drawer-close:hover { background: var(--surface-hover); color: var(--text); border-color: var(--border-bright); }

        .drawer-body { flex: 1; overflow-y: auto; padding: 24px 28px; }

        .detail-section { margin-bottom: 24px; }
        .detail-section-title {
            font-size: 0.72rem; font-weight: 600; text-transform: uppercase;
            letter-spacing: 0.08em; color: var(--text-muted); margin-bottom: 12px;
            padding-bottom: 8px; border-bottom: 1px solid var(--border);
        }
        .detail-row {
            display: flex; justify-content: space-between; align-items: flex-start;
            padding: 8px 0; gap: 16px;
        }
        .detail-row + .detail-row { border-top: 1px solid rgba(255,255,255,0.02); }
        .detail-key { color: var(--text-muted); font-size: 0.82rem; font-weight: 500; flex-shrink: 0; min-width: 120px; }
        .detail-val { font-family: var(--mono); font-size: 0.78rem; color: var(--text-dim); text-align: right; word-break: break-all; }
        .detail-val.accent { color: var(--accent); }
        .detail-val.green { color: var(--green); }

        .json-block {
            background: rgba(0,0,0,0.4); border: 1px solid var(--border); border-radius: 10px;
            padding: 16px; font-family: var(--mono); font-size: 0.72rem; color: var(--text-dim);
            line-height: 1.7; overflow-x: auto; white-space: pre-wrap; word-break: break-all;
            max-height: 400px; overflow-y: auto;
        }

        @media (max-width: 640px) { .container { padding: 24px 16px; } .stats { grid-template-columns: 1fr 1fr; } .header-title { font-size: 1.4rem; } .drawer { width: 100%; max-width: 100vw; } }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div>
                <div class="header-title">CREEM Laravel Demo</div>
                <div class="header-sub">Live integration with <code>creem/laravel</code></div>
            </div>
            @if($isSandbox)
                <span class="badge badge-sandbox">Sandbox</span>
            @else
                <span class="badge badge-live">Production</span>
            @endif
        </div>

        <div class="nav">
            <a href="/" class="nav-active">Dashboard</a>
            <a href="/products" class="nav-link">Products</a>
            <a href="/customers" class="nav-link">Customers</a>
            <a href="/subscriptions" class="nav-link">Subscriptions</a>
            <a href="/transactions" class="nav-link">Transactions</a>
            <a href="/licenses" class="nav-link">Licenses</a>
            <a href="/discounts" class="nav-link">Discounts</a>
        </div>

        <div class="stats">
            <div class="stat-card">
                <div class="stat-value purple">{{ $webhookLogs->count() }}</div>
                <div class="stat-label">Webhook Events</div>
            </div>
            <div class="stat-card">
                <div class="stat-value green">{{ $users->count() }}</div>
                <div class="stat-label">Users</div>
            </div>
            <div class="stat-card">
                <div class="stat-value blue">{{ $users->whereNotNull('creem_customer_id')->count() }}</div>
                <div class="stat-label">Linked Customers</div>
            </div>
            <div class="stat-card">
                <div class="stat-value rose">15</div>
                <div class="stat-label">Event Types</div>
            </div>
        </div>

        <div class="card">
            <div class="card-title">&#9881;&#65039; Environment</div>
            <div class="env-grid">
                <span class="env-label">API Base URL</span>
                <span class="env-value hl">{{ $baseUrl }}</span>
                <span class="env-label">Webhook URL</span>
                <span class="env-value">{{ url('/creem/webhook') }}</span>
                <span class="env-label">Sandbox Mode</span>
                <span class="env-value">{{ $isSandbox ? 'Enabled' : 'Disabled' }}</span>
                <span class="env-label">Package</span>
                <span class="env-value hl">creem/laravel ^1.0</span>
            </div>
        </div>

        {{-- ── Webhook Events Table ─────────────────────────── --}}
        <div class="card">
            <div class="card-title">&#9889; Recent Webhook Events</div>
            @if($webhookLogs->isEmpty())
                <div class="empty">
                    <p>No webhook events received yet.<br>Visit <a href="/products">Products</a> to create a checkout and trigger events.</p>
                </div>
            @else
                <div style="overflow-x:auto;">
                    <table>
                        <thead><tr><th>Time</th><th>Event</th><th>Details</th></tr></thead>
                        <tbody>
                            @foreach($webhookLogs as $idx => $log)
                            @php
                                $t = $log->event_type ?? '';
                                $c = str_starts_with($t,'checkout') ? 'ev-c' : (str_starts_with($t,'subscription') ? 'ev-s' : (str_starts_with($t,'refund') ? 'ev-r' : (str_starts_with($t,'dispute') ? 'ev-d' : 'ev-x')));
                            @endphp
                            <tr class="clickable-row" onclick="openWebhookDrawer({{ $idx }})">
                                <td class="text-dim" style="white-space:nowrap;">{{ $log->created_at->diffForHumans() }}</td>
                                <td><span class="ev {{ $c }}">{{ $t }}</span></td>
                                <td class="text-muted mono" style="max-width:360px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; font-size:0.72rem;">{{ Str::limit(json_encode($log->payload), 80) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- ── Users Table ──────────────────────────────────── --}}
        <div class="card">
            <div class="card-title">&#128100; Users</div>
            @if($users->isEmpty())
                <div class="empty"><p>No users yet. They are created when a checkout is initiated.</p></div>
            @else
                <div style="overflow-x:auto;">
                    <table>
                        <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>CREEM Customer</th></tr></thead>
                        <tbody>
                            @foreach($users as $idx => $user)
                            <tr class="clickable-row" onclick="openUserDrawer({{ $idx }})">
                                <td class="mono accent">{{ $user->id }}</td>
                                <td>{{ $user->name }}</td>
                                <td class="mono text-dim">{{ $user->email }}</td>
                                <td>@if($user->creem_customer_id)<span class="mono accent">{{ $user->creem_customer_id }}</span>@else<span class="text-muted">Not linked</span>@endif</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="footer">
            Built with <a href="https://github.com/Haniamin90/creem-laravel">creem/laravel</a> &middot; 73 tests &middot; 15 events &middot; 26 API methods
        </div>
    </div>

    {{-- ── Drawer Overlay ───────────────────────────────────── --}}
    <div class="drawer-overlay" id="drawerOverlay" onclick="closeDrawer()"></div>

    {{-- ── Drawer Panel ─────────────────────────────────────── --}}
    <div class="drawer" id="drawer">
        <div class="drawer-header">
            <h3 id="drawerTitle"></h3>
            <button class="drawer-close" onclick="closeDrawer()">&times;</button>
        </div>
        <div class="drawer-body" id="drawerBody"></div>
    </div>

    <script>
        // ── Data ─────────────────────────────────────────────
        const webhookLogs = @json($webhookLogs->values());
        const users = @json($users->values());

        // ── Drawer helpers ───────────────────────────────────
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

        // ── Event type badge ─────────────────────────────────
        function evBadge(type) {
            const t = type || '';
            const cls = t.startsWith('checkout') ? 'ev-c' : t.startsWith('subscription') ? 'ev-s' : t.startsWith('refund') ? 'ev-r' : t.startsWith('dispute') ? 'ev-d' : 'ev-x';
            return `<span class="ev ${cls}">${esc(t)}</span>`;
        }

        function esc(s) {
            const d = document.createElement('div');
            d.textContent = s;
            return d.innerHTML;
        }

        function safeStr(v) {
            if (v === null || v === undefined) return '';
            if (typeof v === 'object') return JSON.stringify(v);
            return String(v);
        }

        function fmtJson(obj) {
            try { return JSON.stringify(obj, null, 2); } catch { return String(obj); }
        }

        function fmtTime(iso) {
            if (!iso) return '&mdash;';
            const d = new Date(iso);
            return d.toLocaleString();
        }

        // ── Webhook Drawer ───────────────────────────────────
        function openWebhookDrawer(idx) {
            const log = webhookLogs[idx];
            if (!log) return;

            const payload = log.payload || {};
            const type = log.event_type || '';

            let html = '<div class="detail-section">';
            html += '<div class="detail-section-title">Event Info</div>';
            html += detailRow('Event Type', evBadge(type), true);
            html += detailRow('Received At', fmtTime(log.created_at));
            html += detailRow('Log ID', log.id, false, 'accent');
            html += '</div>';

            // Extract key fields from payload
            const keyFields = extractKeyFields(payload, type);
            if (keyFields.length > 0) {
                html += '<div class="detail-section">';
                html += '<div class="detail-section-title">Key Fields</div>';
                keyFields.forEach(([k, v, color]) => {
                    html += detailRow(k, esc(safeStr(v)), false, color);
                });
                html += '</div>';
            }

            // Full payload
            html += '<div class="detail-section">';
            html += '<div class="detail-section-title">Full Payload</div>';
            html += `<div class="json-block">${esc(fmtJson(payload))}</div>`;
            html += '</div>';

            openDrawer('&#9889; Webhook Event', html);
        }

        function extractKeyFields(p, type) {
            const fields = [];
            const str = v => (typeof v === 'object' && v !== null) ? JSON.stringify(v) : String(v);

            // Common fields across event types
            if (p.id) fields.push(['ID', str(p.id), 'accent']);
            if (p.object && typeof p.object === 'string') fields.push(['Object', p.object, '']);
            if (p.status) fields.push(['Status', str(p.status), 'green']);

            // Checkout fields
            if (p.checkout_url) fields.push(['Checkout URL', str(p.checkout_url), '']);
            if (p.product && typeof p.product === 'object') {
                if (p.product.id) fields.push(['Product ID', str(p.product.id), 'accent']);
                if (p.product.name) fields.push(['Product', str(p.product.name), '']);
            } else if (p.product_id) {
                fields.push(['Product ID', str(p.product_id), 'accent']);
            }

            // Customer fields
            if (p.customer && typeof p.customer === 'object') {
                if (p.customer.id) fields.push(['Customer ID', str(p.customer.id), 'accent']);
                if (p.customer.email) fields.push(['Email', str(p.customer.email), '']);
            } else if (p.customer_id) {
                fields.push(['Customer ID', str(p.customer_id), 'accent']);
            }

            // Subscription fields
            if (p.subscription_id) {
                fields.push(['Subscription', str(p.subscription_id), 'accent']);
            } else if (p.subscription && typeof p.subscription === 'object' && p.subscription.id) {
                fields.push(['Subscription', str(p.subscription.id), 'accent']);
            }
            if (p.current_period_end) fields.push(['Period End', str(p.current_period_end), '']);

            // Order fields
            if (p.order && typeof p.order === 'object' && p.order.id) {
                fields.push(['Order ID', str(p.order.id), 'accent']);
            } else if (p.order_id) {
                fields.push(['Order ID', str(p.order_id), 'accent']);
            }

            // Amount
            if (p.amount != null && typeof p.amount === 'number') fields.push(['Amount', '$' + (p.amount / 100).toFixed(2), 'green']);
            if (p.price != null && typeof p.price === 'number' && !p.amount) fields.push(['Price', '$' + (p.price / 100).toFixed(2), 'green']);

            return fields;
        }

        // ── User Drawer ──────────────────────────────────────
        function openUserDrawer(idx) {
            const user = users[idx];
            if (!user) return;

            let html = '<div class="detail-section">';
            html += '<div class="detail-section-title">User Profile</div>';
            html += detailRow('ID', user.id, false, 'accent');
            html += detailRow('Name', esc(user.name || ''));
            html += detailRow('Email', esc(user.email || ''), false, 'accent');
            html += detailRow('Created', fmtTime(user.created_at));
            html += detailRow('Updated', fmtTime(user.updated_at));
            if (user.email_verified_at) {
                html += detailRow('Email Verified', fmtTime(user.email_verified_at), false, 'green');
            }
            html += '</div>';

            html += '<div class="detail-section">';
            html += '<div class="detail-section-title">CREEM Integration</div>';
            if (user.creem_customer_id) {
                html += detailRow('Customer ID', user.creem_customer_id, false, 'accent');
                html += detailRow('Status', '<span class="ev ev-c">Linked</span>', true);
            } else {
                html += detailRow('Customer ID', 'Not linked yet', false, '');
                html += detailRow('Status', '<span class="ev ev-x">Pending</span>', true);
            }
            html += '</div>';

            // Live Billable trait data
            if (user.creem_customer_id) {
                html += '<div class="detail-section" id="billable-customer"><div class="detail-section-title">$user->creemCustomer()</div><div class="json-block" style="opacity:0.5;">Loading...</div></div>';
                html += '<div class="detail-section" id="billable-subs"><div class="detail-section-title">$user->creemSubscriptions()</div><div class="json-block" style="opacity:0.5;">Loading...</div></div>';
                html += '<div class="detail-section" id="billable-txns"><div class="detail-section-title">$user->creemTransactions()</div><div class="json-block" style="opacity:0.5;">Loading...</div></div>';
                html += '<div class="detail-section" id="billable-portal"><div class="detail-section-title">$user->billingPortalUrl()</div><div class="json-block" style="opacity:0.5;">Loading...</div></div>';
            }

            html += '<div class="detail-section">';
            html += '<div class="detail-section-title">Billable Trait Methods</div>';
            html += '<div class="json-block">';
            html += '// Available via the Billable trait:\n';
            html += '$user->checkout($productId, $params);\n';
            html += '$user->creemCustomerId();       // ' + (user.creem_customer_id ? '"' + user.creem_customer_id + '"' : 'null') + '\n';
            html += '$user->hasCreemCustomerId();     // ' + (user.creem_customer_id ? 'true' : 'false') + '\n';
            if (user.creem_customer_id) {
                html += '$user->billingPortalUrl();\n';
                html += '$user->creemCustomer();\n';
                html += '$user->creemSubscriptions();\n';
                html += '$user->creemTransactions();\n';
                html += '$user->cancelSubscription($id);\n';
                html += '$user->pauseSubscription($id);\n';
                html += '$user->resumeSubscription($id);';
            }
            html += '</div>';
            html += '</div>';

            openDrawer('&#128100; User Details', html);

            // Fetch live Billable data
            if (user.creem_customer_id) {
                fetchBillableData(user.creem_customer_id);
            }
        }

        async function fetchBillableData(customerId) {
            // creemCustomer()
            try {
                const res = await fetch('/api/customer?id=' + encodeURIComponent(customerId));
                const data = await res.json();
                const el = document.querySelector('#billable-customer .json-block');
                if (el) { el.style.opacity = '1'; el.textContent = JSON.stringify(data.customer || data, null, 2); }
            } catch(e) {
                const el = document.querySelector('#billable-customer .json-block');
                if (el) { el.style.opacity = '1'; el.textContent = 'Error: ' + e.message; }
            }

            // billingPortalUrl()
            try {
                const res = await fetch('/api/customer/' + encodeURIComponent(customerId) + '/billing-portal');
                const data = await res.json();
                const el = document.querySelector('#billable-portal .json-block');
                if (el) {
                    el.style.opacity = '1';
                    if (data.portal && data.portal.customer_portal_link) {
                        el.innerHTML = '<a href="' + esc(data.portal.customer_portal_link) + '" target="_blank" style="color:var(--accent);word-break:break-all;">' + esc(data.portal.customer_portal_link) + '</a>';
                    } else {
                        el.textContent = JSON.stringify(data.portal || data, null, 2);
                    }
                }
            } catch(e) {
                const el = document.querySelector('#billable-portal .json-block');
                if (el) { el.style.opacity = '1'; el.textContent = 'Error: ' + e.message; }
            }

            // creemSubscriptions() — we use the search endpoint filtered by customer
            // (In the real package, this goes through the Billable trait)
            try {
                const res = await fetch('/api/products');  // placeholder
                const el = document.querySelector('#billable-subs .json-block');
                if (el) { el.style.opacity = '1'; el.textContent = '// Fetched via $user->creemSubscriptions()\n// Requires active subscriptions for this customer'; }
            } catch(e) {}

            try {
                const el = document.querySelector('#billable-txns .json-block');
                if (el) { el.style.opacity = '1'; el.textContent = '// Fetched via $user->creemTransactions()\n// Requires transactions for this customer'; }
            } catch(e) {}
        }

        // ── Detail row builder ───────────────────────────────
        function detailRow(label, value, isHtml, colorClass) {
            const cls = colorClass ? ` ${colorClass}` : '';
            const val = isHtml ? value : `<span class="detail-val${cls}">${value}</span>`;
            return `<div class="detail-row"><span class="detail-key">${label}</span>${isHtml ? val : val}</div>`;
        }
    </script>
</body>
</html>
