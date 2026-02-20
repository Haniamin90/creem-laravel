<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products &mdash; CREEM Laravel Demo</title>
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
        .products-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 20px; }
        .product-card { background: var(--surface); backdrop-filter: blur(20px); border: 1px solid var(--border); border-radius: 20px; padding: 32px; transition: all 0.35s cubic-bezier(0.4,0,0.2,1); position: relative; overflow: hidden; }
        .product-card:hover { border-color: var(--border-bright); transform: translateY(-4px); box-shadow: 0 20px 60px rgba(0,0,0,0.4); }
        .product-card::before { content:''; position: absolute; top: 0; left: 0; right: 0; height: 3px; border-radius: 20px 20px 0 0; }
        .product-card.recurring::before { background: linear-gradient(90deg, var(--accent), var(--blue)); }
        .product-card.onetime::before { background: linear-gradient(90deg, var(--green), var(--amber)); }
        .product-type { display: inline-flex; align-items: center; gap: 5px; padding: 4px 12px; border-radius: 8px; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 16px; }
        .type-recurring { background: rgba(124,92,252,0.12); color: var(--accent); }
        .type-onetime { background: rgba(52,211,153,0.12); color: var(--green); }
        .product-name { font-size: 1.25rem; font-weight: 700; margin-bottom: 6px; letter-spacing: -0.01em; }
        .product-desc { color: var(--text-dim); font-size: 0.85rem; line-height: 1.5; margin-bottom: 20px; min-height: 40px; }
        .product-price { margin-bottom: 24px; }
        .price-amount { font-size: 2.4rem; font-weight: 800; letter-spacing: -0.03em; }
        .price-amount.purple { color: var(--accent); }
        .price-amount.green { color: var(--green); }
        .price-period { font-size: 0.82rem; color: var(--text-muted); font-weight: 400; }
        .price-currency { font-size: 0.85rem; color: var(--text-muted); font-weight: 500; margin-left: 4px; }
        .product-id { font-family: var(--mono); font-size: 0.72rem; color: var(--text-muted); margin-bottom: 20px; }
        .checkout-form { display: flex; gap: 10px; flex-wrap: wrap; }
        .input { padding: 10px 14px; background: rgba(0,0,0,0.3); border: 1px solid var(--border); border-radius: 10px; color: var(--text); font-family: 'Inter',sans-serif; font-size: 0.84rem; flex: 1; min-width: 180px; transition: border-color 0.2s; outline: none; }
        .input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-glow); }
        .input::placeholder { color: var(--text-muted); }
        .btn { padding: 10px 24px; background: var(--accent); color: #fff; border: none; border-radius: 10px; font-family: 'Inter',sans-serif; font-size: 0.84rem; font-weight: 600; cursor: pointer; transition: all 0.2s; white-space: nowrap; }
        .btn:hover { background: #6a4ae8; box-shadow: 0 4px 20px var(--accent-glow); transform: translateY(-1px); }
        .btn:active { transform: translateY(0); }
        .empty-card { background: var(--surface); border: 1px dashed var(--border-bright); border-radius: 20px; padding: 60px 20px; text-align: center; }
        .empty-card p { color: var(--text-muted); font-size: 0.88rem; line-height: 1.6; }
        .empty-card a { color: var(--accent); text-decoration: none; }
        .success-box { background: rgba(52,211,153,0.08); border: 1px solid rgba(52,211,153,0.2); border-radius: 12px; padding: 16px 20px; color: var(--green); font-size: 0.88rem; margin-bottom: 24px; display: flex; align-items: center; gap: 10px; }
        .btn-seed { padding: 10px 24px; background: rgba(52,211,153,0.12); color: var(--green); border: 1px solid rgba(52,211,153,0.2); border-radius: 10px; font-family: 'Inter',sans-serif; font-size: 0.84rem; font-weight: 600; cursor: pointer; transition: all 0.2s; white-space: nowrap; display: inline-flex; align-items: center; gap: 8px; }
        .btn-seed:hover { background: rgba(52,211,153,0.2); border-color: rgba(52,211,153,0.35); transform: translateY(-1px); }
        .btn-seed:active { transform: translateY(0); }
        .seed-hint { font-family: var(--mono); font-size: 0.72rem; color: var(--text-muted); margin-top: 16px; }
        .footer { margin-top: 48px; padding-top: 24px; border-top: 1px solid var(--border); text-align: center; color: var(--text-muted); font-size: 0.78rem; }
        .footer a { color: var(--accent); text-decoration: none; }
        @media (max-width: 640px) { .container { padding: 24px 16px; } .products-grid { grid-template-columns: 1fr; } .price-amount { font-size: 2rem; } }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-title">Products</div>
            <div class="header-sub">Fetched live from CREEM API via <code>Creem::searchProducts()</code></div>
        </div>

        <div class="nav">
            <a href="/" class="nav-link">&larr; Dashboard</a>
            <a href="/products" class="nav-active">Products</a>
        </div>

        @if(session('success'))
            <div class="success-box">{{ session('success') }}</div>
        @endif

        @if($error ?? false)
            <div class="error-box">{{ $error }}</div>
        @endif

        @if(session('error'))
            <div class="error-box">{{ session('error') }}</div>
        @endif

        @if(count($products) > 0)
            <div class="products-grid">
                @foreach($products as $product)
                @php
                    $isRecurring = ($product['billing_type'] ?? '') === 'recurring';
                    $period = str_replace('every-', '', $product['billing_period'] ?? '');
                @endphp
                <div class="product-card {{ $isRecurring ? 'recurring' : 'onetime' }}">
                    <div class="product-type {{ $isRecurring ? 'type-recurring' : 'type-onetime' }}">
                        {{ $isRecurring ? 'Subscription' : 'One-time' }}
                        @if($isRecurring && $period)
                            &middot; {{ $period }}
                        @endif
                    </div>
                    <div class="product-name">{{ $product['name'] ?? 'Unnamed Product' }}</div>
                    <div class="product-desc">{{ $product['description'] ?? 'No description' }}</div>
                    <div class="product-price">
                        <span class="price-amount {{ $isRecurring ? 'purple' : 'green' }}">${{ number_format(($product['price'] ?? 0) / 100, 2) }}</span>
                        <span class="price-currency">{{ strtoupper($product['currency'] ?? 'USD') }}</span>
                        @if($isRecurring)
                            <span class="price-period">/ {{ $period }}</span>
                        @endif
                    </div>
                    <div class="product-id">{{ $product['id'] ?? '' }}</div>
                    <form method="POST" action="/checkout" class="checkout-form">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product['id'] }}">
                        <input type="email" name="email" class="input" placeholder="your@email.com" value="demo@creem-laravel.test">
                        <button type="submit" class="btn">Checkout &rarr;</button>
                    </form>
                </div>
                @endforeach
            </div>
        @else
            <div class="empty-card">
                <p>No products found in your CREEM account.</p>
                <p style="margin-top: 16px;">
                    <form method="POST" action="/seed-products" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn-seed">&#9889; Create Sample Products</button>
                    </form>
                </p>
                <p class="seed-hint">Creates 4 products via Creem::createProduct() &mdash; 3 subscriptions + 1 one-time</p>
                <p style="margin-top: 12px;"><span class="text-muted" style="font-size:0.82rem;">Or create them manually in your <a href="https://creem.io/dashboard">CREEM dashboard</a></span></p>
            </div>
        @endif

        <div class="footer">
            Built with <a href="https://github.com/Haniamin90/creem-laravel">creem/laravel</a> &middot; 73 tests &middot; 15 events &middot; 26 API methods
        </div>
    </div>
</body>
</html>
