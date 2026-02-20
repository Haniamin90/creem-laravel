<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscriptions &mdash; CREEM Laravel Demo</title>
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
        .status { display: inline-block; padding: 2px 10px; border-radius: 6px; font-size: 0.72rem; font-weight: 600; text-transform: uppercase; }
        .status-active { background: rgba(52,211,153,0.1); color: var(--green); }
        .status-canceled, .status-expired { background: rgba(251,113,133,0.1); color: var(--rose); }
        .status-paused { background: rgba(251,191,36,0.1); color: var(--amber); }
        .status-trialing { background: rgba(96,165,250,0.1); color: var(--blue); }
        .status-past_due { background: rgba(251,113,133,0.1); color: var(--rose); }
        .btn-sm { padding: 5px 12px; border: none; border-radius: 6px; font-size: 0.72rem; font-weight: 600; cursor: pointer; transition: all 0.2s; margin: 2px; }
        .btn-cancel { background: rgba(251,113,133,0.12); color: var(--rose); border: 1px solid rgba(251,113,133,0.2); }
        .btn-cancel:hover { background: rgba(251,113,133,0.2); }
        .btn-pause { background: rgba(251,191,36,0.12); color: var(--amber); border: 1px solid rgba(251,191,36,0.2); }
        .btn-pause:hover { background: rgba(251,191,36,0.2); }
        .btn-resume { background: rgba(52,211,153,0.12); color: var(--green); border: 1px solid rgba(52,211,153,0.2); }
        .btn-resume:hover { background: rgba(52,211,153,0.2); }
        .btn-upgrade { background: rgba(96,165,250,0.12); color: var(--blue); border: 1px solid rgba(96,165,250,0.2); }
        .btn-upgrade:hover { background: rgba(96,165,250,0.2); }
        .actions-cell { white-space: nowrap; }
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
            <div class="header-title">Subscriptions</div>
            <div class="header-sub">Manage via <code>Creem::searchSubscriptions()</code> &middot; 7 subscription methods</div>
        </div>

        <div class="nav">
            <a href="/" class="nav-link">&larr; Dashboard</a>
            <a href="/products" class="nav-link">Products</a>
            <a href="/customers" class="nav-link">Customers</a>
            <a href="/subscriptions" class="nav-active">Subscriptions</a>
            <a href="/transactions" class="nav-link">Transactions</a>
            <a href="/licenses" class="nav-link">Licenses</a>
            <a href="/discounts" class="nav-link">Discounts</a>
        </div>

        @if(session('success'))
            <div class="success-box">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="error-box">{{ session('error') }}</div>
        @endif
        @if($error ?? false)
            <div class="error-box">{{ $error }}</div>
        @endif

        {{-- Lookup by ID --}}
        <div class="card">
            <div style="font-size:0.95rem;font-weight:600;margin-bottom:16px;">&#128270; Lookup Subscription</div>
            <form method="GET" action="/subscriptions" style="display:flex;gap:10px;flex-wrap:wrap;">
                <input type="text" name="id" class="input-lookup" placeholder="sub_..." value="{{ $lookupId ?? '' }}" style="padding:10px 14px;background:rgba(0,0,0,0.3);border:1px solid var(--border);border-radius:10px;color:var(--text);font-family:'Inter',sans-serif;font-size:0.84rem;flex:1;min-width:220px;outline:none;">
                <button type="submit" class="btn-sm btn-upgrade" style="padding:10px 20px;">Lookup via Creem::getSubscription()</button>
            </form>
        </div>

        {{-- Looked-up subscription result --}}
        @if($subscription ?? false)
        @php
            $status = $subscription['status'] ?? 'unknown';
            $statusCls = match($status) {
                'active' => 'status-active',
                'canceled' => 'status-canceled',
                'paused' => 'status-paused',
                'trialing' => 'status-trialing',
                'past_due' => 'status-past_due',
                'expired' => 'status-expired',
                default => ''
            };
        @endphp
        <div class="card" style="border-color:rgba(96,165,250,0.2);">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
                <div>
                    <span class="status {{ $statusCls }}">{{ $status }}</span>
                    <span class="mono accent" style="margin-left:10px;">{{ $subscription['id'] ?? '' }}</span>
                </div>
                <div class="actions-cell">
                    @if($status === 'active')
                        <form method="POST" action="/subscriptions/pause" style="display:inline;">@csrf<input type="hidden" name="subscription_id" value="{{ $subscription['id'] }}"><button class="btn-sm btn-pause" type="submit">Pause</button></form>
                        <form method="POST" action="/subscriptions/cancel" style="display:inline;">@csrf<input type="hidden" name="subscription_id" value="{{ $subscription['id'] }}"><input type="hidden" name="mode" value="scheduled"><button class="btn-sm btn-cancel" type="submit">Cancel</button></form>
                    @elseif($status === 'paused')
                        <form method="POST" action="/subscriptions/resume" style="display:inline;">@csrf<input type="hidden" name="subscription_id" value="{{ $subscription['id'] }}"><button class="btn-sm btn-resume" type="submit">Resume</button></form>
                    @endif
                </div>
            </div>
            <div style="background:rgba(0,0,0,0.4);border:1px solid var(--border);border-radius:10px;padding:16px;font-family:var(--mono);font-size:0.72rem;color:var(--text-dim);line-height:1.7;overflow-x:auto;white-space:pre-wrap;word-break:break-all;max-height:400px;overflow-y:auto;">{{ json_encode($subscription, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</div>
        </div>
        @endif

        {{-- Subscription table (if search endpoint is available) --}}
        @if(count($subscriptions) > 0)
        <div class="card">
            <div style="overflow-x:auto;">
                <table>
                    <thead><tr><th>Subscription ID</th><th>Status</th><th>Product</th><th>Customer</th><th>Actions</th></tr></thead>
                    <tbody>
                        @foreach($subscriptions as $idx => $sub)
                        @php
                            $status = $sub['status'] ?? 'unknown';
                            $statusCls = match($status) {
                                'active' => 'status-active',
                                'canceled' => 'status-canceled',
                                'paused' => 'status-paused',
                                'trialing' => 'status-trialing',
                                'past_due' => 'status-past_due',
                                'expired' => 'status-expired',
                                default => ''
                            };
                        @endphp
                        <tr class="clickable-row" onclick="openSubDrawer({{ $idx }})">
                            <td class="mono accent">{{ Str::limit($sub['id'] ?? '', 24) }}</td>
                            <td><span class="status {{ $statusCls }}">{{ $status }}</span></td>
                            <td class="text-dim" style="font-size:0.82rem;">{{ $sub['product']['name'] ?? ($sub['product_id'] ?? 'N/A') }}</td>
                            <td class="mono text-dim" style="font-size:0.75rem;">{{ Str::limit($sub['customer_id'] ?? ($sub['customer']['id'] ?? ''), 20) }}</td>
                            <td class="actions-cell" onclick="event.stopPropagation();">
                                @if($status === 'active')
                                    <form method="POST" action="/subscriptions/pause" style="display:inline;">@csrf<input type="hidden" name="subscription_id" value="{{ $sub['id'] }}"><button class="btn-sm btn-pause" type="submit">Pause</button></form>
                                    <form method="POST" action="/subscriptions/cancel" style="display:inline;">@csrf<input type="hidden" name="subscription_id" value="{{ $sub['id'] }}"><input type="hidden" name="mode" value="scheduled"><button class="btn-sm btn-cancel" type="submit">Cancel</button></form>
                                @elseif($status === 'paused')
                                    <form method="POST" action="/subscriptions/resume" style="display:inline;">@csrf<input type="hidden" name="subscription_id" value="{{ $sub['id'] }}"><button class="btn-sm btn-resume" type="submit">Resume</button></form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @elseif(!($subscription ?? false) && !($lookupId ?? false))
        <div class="card">
            <div class="empty">
                <p>Enter a subscription ID above to look it up, or purchase a <a href="/products">recurring product</a> to create one.</p>
            </div>
        </div>
        @endif

        <div class="footer">
            Built with <a href="https://github.com/Haniamin90/creem-laravel">creem/laravel</a> &middot; <code>searchSubscriptions</code> &middot; <code>getSubscription</code> &middot; <code>cancel</code> &middot; <code>pause</code> &middot; <code>resume</code> &middot; <code>upgrade</code>
        </div>
    </div>

    <div class="drawer-overlay" id="drawerOverlay" onclick="closeDrawer()"></div>
    <div class="drawer" id="drawer">
        <div class="drawer-header"><h3 id="drawerTitle"></h3><button class="drawer-close" onclick="closeDrawer()">&times;</button></div>
        <div class="drawer-body" id="drawerBody"></div>
    </div>

    <script>
        const subscriptions = @json(collect($subscriptions)->values());

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

        function openSubDrawer(idx) {
            const s = subscriptions[idx];
            if (!s) return;

            const status = s.status || 'unknown';
            const statusCls = {'active':'status-active','canceled':'status-canceled','paused':'status-paused','trialing':'status-trialing','past_due':'status-past_due','expired':'status-expired'}[status] || '';

            let html = '<div class="detail-section"><div class="detail-section-title">Subscription Info</div>';
            html += row('ID', s.id, 'accent');
            html += row('Status', `<span class="status ${statusCls}">${esc(status)}</span>`, '', true);
            html += row('Product', safeStr(s.product?.name || s.product_id || ''));
            html += row('Product ID', safeStr(s.product?.id || s.product_id || ''), 'accent');
            html += row('Customer ID', safeStr(s.customer?.id || s.customer_id || ''), 'accent');
            if (s.current_period_start) html += row('Period Start', s.current_period_start);
            if (s.current_period_end) html += row('Period End', s.current_period_end);
            if (s.cancel_at) html += row('Cancel At', s.cancel_at);
            html += '</div>';

            // Actions
            html += '<div class="detail-section"><div class="detail-section-title">Actions</div>';
            html += '<div style="display:flex;gap:8px;flex-wrap:wrap;">';
            if (status === 'active') {
                html += `<form method="POST" action="/subscriptions/pause">@csrf_token<input type="hidden" name="subscription_id" value="${esc(s.id)}"><button class="btn-sm btn-pause" type="submit">Pause</button></form>`;
                html += `<form method="POST" action="/subscriptions/cancel">@csrf_token<input type="hidden" name="subscription_id" value="${esc(s.id)}"><input type="hidden" name="mode" value="scheduled"><button class="btn-sm btn-cancel" type="submit">Cancel (Scheduled)</button></form>`;
                html += `<form method="POST" action="/subscriptions/cancel">@csrf_token<input type="hidden" name="subscription_id" value="${esc(s.id)}"><input type="hidden" name="mode" value="immediate"><button class="btn-sm btn-cancel" type="submit">Cancel (Immediate)</button></form>`;
            } else if (status === 'paused') {
                html += `<form method="POST" action="/subscriptions/resume">@csrf_token<input type="hidden" name="subscription_id" value="${esc(s.id)}"><button class="btn-sm btn-resume" type="submit">Resume</button></form>`;
            }
            html += '</div></div>';

            html += '<div class="detail-section"><div class="detail-section-title">Full Response</div>';
            html += `<div class="json-block">${esc(JSON.stringify(s, null, 2))}</div></div>`;

            openDrawer('&#128260; Subscription Details', html);

            // Inject CSRF tokens
            document.querySelectorAll('.drawer-body form').forEach(form => {
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = '{{ csrf_token() }}';
                form.prepend(csrfInput);
            });
        }

        function row(label, value, cls, isHtml) {
            if (isHtml) return `<div class="detail-row"><span class="detail-key">${label}</span>${value}</div>`;
            return `<div class="detail-row"><span class="detail-key">${label}</span><span class="detail-val ${cls || ''}">${esc(safeStr(value))}</span></div>`;
        }
    </script>
</body>
</html>
