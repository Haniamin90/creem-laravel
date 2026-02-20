<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Licenses &mdash; CREEM Laravel Demo</title>
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
        .card-title { font-size: 0.95rem; font-weight: 600; margin-bottom: 16px; display: flex; align-items: center; gap: 10px; }
        .form-group { margin-bottom: 16px; }
        .form-label { display: block; font-size: 0.78rem; font-weight: 500; color: var(--text-dim); margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.06em; }
        .input { width: 100%; padding: 10px 14px; background: rgba(0,0,0,0.3); border: 1px solid var(--border); border-radius: 10px; color: var(--text); font-family: 'Inter',sans-serif; font-size: 0.84rem; transition: border-color 0.2s; outline: none; }
        .input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-glow); }
        .input::placeholder { color: var(--text-muted); }
        .btn { padding: 10px 24px; background: var(--accent); color: #fff; border: none; border-radius: 10px; font-family: 'Inter',sans-serif; font-size: 0.84rem; font-weight: 600; cursor: pointer; transition: all 0.2s; white-space: nowrap; }
        .btn:hover { background: #6a4ae8; box-shadow: 0 4px 20px var(--accent-glow); transform: translateY(-1px); }
        .btn-green { background: rgba(52,211,153,0.15); color: var(--green); border: 1px solid rgba(52,211,153,0.25); }
        .btn-green:hover { background: rgba(52,211,153,0.25); box-shadow: none; }
        .btn-rose { background: rgba(251,113,133,0.15); color: var(--rose); border: 1px solid rgba(251,113,133,0.25); }
        .btn-rose:hover { background: rgba(251,113,133,0.25); box-shadow: none; }
        .btn-blue { background: rgba(96,165,250,0.15); color: var(--blue); border: 1px solid rgba(96,165,250,0.25); }
        .btn-blue:hover { background: rgba(96,165,250,0.25); box-shadow: none; }
        .forms-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; }
        .mono { font-family: var(--mono); font-size: 0.78rem; }
        .json-block { background: rgba(0,0,0,0.4); border: 1px solid var(--border); border-radius: 10px; padding: 16px; font-family: var(--mono); font-size: 0.72rem; color: var(--text-dim); line-height: 1.7; overflow-x: auto; white-space: pre-wrap; word-break: break-all; max-height: 400px; overflow-y: auto; }
        .result-badge { display: inline-block; padding: 3px 12px; border-radius: 6px; font-size: 0.72rem; font-weight: 600; text-transform: uppercase; margin-bottom: 12px; }
        .badge-activate { background: rgba(52,211,153,0.1); color: var(--green); }
        .badge-validate { background: rgba(96,165,250,0.1); color: var(--blue); }
        .badge-deactivate { background: rgba(251,113,133,0.1); color: var(--rose); }
        .footer { margin-top: 48px; padding-top: 24px; border-top: 1px solid var(--border); text-align: center; color: var(--text-muted); font-size: 0.78rem; }
        .footer a { color: var(--accent); text-decoration: none; }
        @media (max-width: 640px) { .container { padding: 24px 16px; } .forms-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-title">Licenses</div>
            <div class="header-sub">Manage via <code>Creem::activateLicense()</code> &middot; <code>validateLicense()</code> &middot; <code>deactivateLicense()</code></div>
        </div>

        <div class="nav">
            <a href="/" class="nav-link">&larr; Dashboard</a>
            <a href="/products" class="nav-link">Products</a>
            <a href="/customers" class="nav-link">Customers</a>
            <a href="/subscriptions" class="nav-link">Subscriptions</a>
            <a href="/transactions" class="nav-link">Transactions</a>
            <a href="/licenses" class="nav-active">Licenses</a>
            <a href="/discounts" class="nav-link">Discounts</a>
        </div>

        @if(session('error'))
            <div class="error-box">{{ session('error') }}</div>
        @endif

        @if($result)
            <div class="card">
                <span class="result-badge badge-{{ $result['action'] }}">{{ $result['action'] }}</span>
                <div class="json-block">{{ json_encode($result['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</div>
            </div>
        @endif

        <div class="forms-grid">
            {{-- Activate --}}
            <div class="card">
                <div class="card-title">&#9889; Activate License</div>
                <form method="POST" action="/licenses/activate">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">License Key</label>
                        <input type="text" name="license_key" class="input" placeholder="XXXX-XXXX-XXXX-XXXX" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Instance Name</label>
                        <input type="text" name="instance_name" class="input" placeholder="my-laptop" value="demo-instance">
                    </div>
                    <button type="submit" class="btn btn-green">Activate</button>
                </form>
                <p class="mono" style="margin-top:12px;color:var(--text-muted);font-size:0.72rem;">Creem::activateLicense($key, $instanceName)</p>
            </div>

            {{-- Validate --}}
            <div class="card">
                <div class="card-title">&#128270; Validate License</div>
                <form method="POST" action="/licenses/validate">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">License Key</label>
                        <input type="text" name="license_key" class="input" placeholder="XXXX-XXXX-XXXX-XXXX" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Instance ID</label>
                        <input type="text" name="instance_id" class="input" placeholder="inst_..." required>
                    </div>
                    <button type="submit" class="btn btn-blue">Validate</button>
                </form>
                <p class="mono" style="margin-top:12px;color:var(--text-muted);font-size:0.72rem;">Creem::validateLicense($key, $instanceId)</p>
            </div>

            {{-- Deactivate --}}
            <div class="card">
                <div class="card-title">&#128683; Deactivate License</div>
                <form method="POST" action="/licenses/deactivate">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">License Key</label>
                        <input type="text" name="license_key" class="input" placeholder="XXXX-XXXX-XXXX-XXXX" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Instance ID</label>
                        <input type="text" name="instance_id" class="input" placeholder="inst_..." required>
                    </div>
                    <button type="submit" class="btn btn-rose">Deactivate</button>
                </form>
                <p class="mono" style="margin-top:12px;color:var(--text-muted);font-size:0.72rem;">Creem::deactivateLicense($key, $instanceId)</p>
            </div>
        </div>

        <div class="footer">
            Built with <a href="https://github.com/Haniamin90/creem-laravel">creem/laravel</a> &middot; <code>activateLicense</code> &middot; <code>validateLicense</code> &middot; <code>deactivateLicense</code>
        </div>
    </div>
</body>
</html>
