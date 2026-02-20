<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful &mdash; CREEM Laravel Demo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --bg: #06060b; --surface: rgba(255,255,255,0.03);
            --border: rgba(255,255,255,0.06); --border-bright: rgba(255,255,255,0.12);
            --text: #f0f0f5; --text-dim: #8888a0; --text-muted: #55556a;
            --accent: #7c5cfc; --accent-glow: rgba(124,92,252,0.15);
            --green: #34d399;
            --mono: 'JetBrains Mono','SF Mono','Fira Code',monospace;
        }
        body { font-family: 'Inter',-apple-system,sans-serif; background: var(--bg); color: var(--text); min-height: 100vh; display: flex; align-items: center; justify-content: center; -webkit-font-smoothing: antialiased; }
        body::before { content:''; position: fixed; top: 20%; left: 30%; width: 40%; height: 40%; background: radial-gradient(ellipse, rgba(52,211,153,0.08) 0%, transparent 70%); pointer-events: none; }

        .card { background: var(--surface); backdrop-filter: blur(20px); border: 1px solid var(--border); border-radius: 24px; padding: 56px 48px; text-align: center; max-width: 520px; width: 100%; margin: 24px; position: relative; overflow: hidden; }
        .card::before { content:''; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, var(--green), var(--accent)); }

        @keyframes pop { 0% { transform: scale(0); opacity: 0; } 50% { transform: scale(1.15); } 100% { transform: scale(1); opacity: 1; } }
        @keyframes fadeUp { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }

        .check-ring { width: 80px; height: 80px; border-radius: 50%; background: rgba(52,211,153,0.1); border: 2px solid rgba(52,211,153,0.25); display: flex; align-items: center; justify-content: center; margin: 0 auto 28px; animation: pop 0.5s ease-out forwards; }
        .check-icon { font-size: 2rem; color: var(--green); }

        h1 { font-size: 1.5rem; font-weight: 700; letter-spacing: -0.02em; margin-bottom: 8px; animation: fadeUp 0.5s 0.15s ease-out both; }
        .subtitle { color: var(--text-dim); font-size: 0.92rem; margin-bottom: 32px; animation: fadeUp 0.5s 0.25s ease-out both; }

        .details { background: rgba(0,0,0,0.25); border: 1px solid var(--border); border-radius: 14px; padding: 20px 24px; margin-bottom: 32px; text-align: left; animation: fadeUp 0.5s 0.35s ease-out both; }
        .detail-row { display: flex; justify-content: space-between; align-items: center; padding: 8px 0; }
        .detail-row + .detail-row { border-top: 1px solid rgba(255,255,255,0.03); }
        .detail-label { color: var(--text-muted); font-size: 0.82rem; font-weight: 500; }
        .detail-value { font-family: var(--mono); font-size: 0.78rem; color: var(--accent); max-width: 220px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

        .actions { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; animation: fadeUp 0.5s 0.45s ease-out both; }
        .btn { padding: 11px 28px; border-radius: 12px; font-family: 'Inter',sans-serif; font-size: 0.85rem; font-weight: 600; text-decoration: none; transition: all 0.2s; cursor: pointer; border: none; }
        .btn-primary { background: var(--accent); color: #fff; box-shadow: 0 4px 20px var(--accent-glow); }
        .btn-primary:hover { background: #6a4ae8; transform: translateY(-1px); }
        .btn-outline { background: transparent; color: var(--text-dim); border: 1px solid var(--border); }
        .btn-outline:hover { border-color: var(--border-bright); color: var(--text); }
    </style>
</head>
<body>
    <div class="card">
        <div class="check-ring">
            <span class="check-icon">&#10003;</span>
        </div>
        <h1>Payment Successful</h1>
        <p class="subtitle">Your checkout has been completed. The webhook event will appear on the dashboard shortly.</p>

        @if($checkoutId || $orderId || $customerId)
        <div class="details">
            @if($checkoutId)
            <div class="detail-row">
                <span class="detail-label">Checkout ID</span>
                <span class="detail-value">{{ $checkoutId }}</span>
            </div>
            @endif
            @if($orderId)
            <div class="detail-row">
                <span class="detail-label">Order ID</span>
                <span class="detail-value">{{ $orderId }}</span>
            </div>
            @endif
            @if($customerId)
            <div class="detail-row">
                <span class="detail-label">Customer ID</span>
                <span class="detail-value">{{ $customerId }}</span>
            </div>
            @endif
        </div>
        @endif

        <div class="actions">
            <a href="/" class="btn btn-primary">&larr; Dashboard</a>
            <a href="/products" class="btn btn-outline">Buy Again</a>
        </div>
    </div>
</body>
</html>
