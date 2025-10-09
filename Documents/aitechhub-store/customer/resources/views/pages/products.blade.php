<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Products - Store</title>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <style>
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; }
        .card { border: 1px solid #e2e8f0; border-radius: 10px; padding: 1rem; background: #fff; transition: all 0.3s; }
        .card img { width: 100%; border-radius: 8px; }
        .card.new-product { animation: fadeIn 0.5s; border-color: #48bb78; }
        .price { font-weight: 700; color: #1e3c72; }
        .stock { font-size: 0.85rem; color: #718096; margin-top: 0.25rem; }
        .stock.low { color: #f56565; font-weight: 600; }
        a { text-decoration: none; color: inherit; }
        .btn { display: inline-block; margin-top: 0.5rem; padding: 0.5rem 1rem; border-radius: 6px; background: #1e3c72; color: #fff; text-decoration: none; border: none; cursor: pointer; }
        .filters { display:flex; gap:0.5rem; align-items:center; margin:1rem 0; flex-wrap:wrap; }
        .filters input, .filters select { padding:0.5rem; }
        .breadcrumb { margin-bottom: 1rem; padding: 0.75rem; background: #f7fafc; border-radius: 6px; font-size: 0.9rem; }
        .breadcrumb a { color: #1e3c72; text-decoration: none; }
        .breadcrumb a:hover { text-decoration: underline; }
        .breadcrumb span { color: #718096; margin: 0 0.5rem; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
        .cart-badge { position: relative; padding: 0.5rem 1rem; background: #1e3c72; color: white; border-radius: 6px; text-decoration: none; display: inline-block; }
        .cart-badge .badge-count { position: absolute; top: -8px; right: -8px; background: #f56565; color: white; border-radius: 50%; padding: 0.25rem 0.5rem; font-size: 0.75rem; font-weight: bold; min-width: 20px; text-align: center; }
        .loading-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.3); display: none; align-items: center; justify-content: center; z-index: 1000; }
        .loading-overlay.active { display: flex; }
        .spinner { border: 4px solid #f3f3f3; border-top: 4px solid #1e3c72; border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        @keyframes fadeIn { from { opacity: 0; transform: scale(0.9); } to { opacity: 1; transform: scale(1); } }
        .live-indicator { display: inline-block; padding: 0.25rem 0.75rem; background: #48bb78; color: white; border-radius: 12px; font-size: 0.75rem; font-weight: 600; }
        .live-indicator::before { content: '●'; margin-right: 0.25rem; animation: pulse 2s infinite; }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }
        .success-message { background: #d1fae5; border: 2px solid #10b981; color: #065f46; padding: 1rem 1.5rem; border-radius: 8px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem; animation: slideDown 0.3s ease-out; }
        @keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body style="font-family: system-ui, sans-serif; padding: 2rem;">
    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner"></div>
    </div>

    <div class="breadcrumb">
        <a href="/dashboard">🏠 Dashboard</a>
        <span>›</span>
        <strong>Products</strong>
    </div>

    <div class="header">
        <div>
            <h1 style="margin: 0;">🛍️ Products <span class="live-indicator">LIVE</span></h1>
        </div>
        @php
            $cart = session('cart', []);
            $cartCount = collect($cart)->sum('quantity');
        @endphp
        <a href="{{ $cartCount > 0 ? (auth()->check() ? '/checkout' : '/login') : '/products' }}" class="cart-badge" id="cartLink">
            🛒 Cart
            @if($cartCount > 0)
                <span class="badge-count" id="cartBadge">{{ $cartCount }}</span>
            @endif
        </a>
    </div>

    @if(session('success'))
        <div class="success-message">
            <span style="font-size: 1.5rem;">✓</span>
            <strong>{{ session('success') }}</strong>
        </div>
    @endif

    <form class="filters" method="GET" action="/products">
        <input type="text" name="q" placeholder="Search products" value="{{ request('q') }}" />
        <select name="type">
            <option value="">All Types</option>
            <option value="one_time" @selected(request('type')==='one_time')>One-time</option>
            <option value="subscription" @selected(request('type')==='subscription')>Subscription</option>
        </select>
        <select name="category">
            <option value="">All Categories</option>
            @foreach(($categories ?? []) as $cat)
                <option value="{{ $cat->slug }}" @selected(request('category')===$cat->slug)>{{ $cat->name }}</option>
            @endforeach
        </select>
        <button class="btn" type="submit">Filter</button>
        <a class="btn" href="/products" style="background:#718096;">Reset</a>
    </form>

    <div class="grid" style="margin-top:1rem;" id="productsGrid">
        @foreach(($products ?? []) as $product)
            <div class="card" data-product-id="{{ $product->id }}" data-product-slug="{{ $product->slug }}">
                @if($product->image_url)
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}">
                @endif
                <h3 style="margin:0.5rem 0;">{{ $product->name }}</h3>
                <div class="price">${{ number_format($product->price, 2) }}</div>
                <div class="stock stock-{{ $product->id }}" @if($product->stock < 5) style="color: #f56565; font-weight: 600;" @endif>
                    Stock: <span class="stock-value">{{ $product->stock }}</span>
                </div>
                <div style="margin-top:0.5rem;">
                    <a href="/products/{{ $product->slug }}" class="btn">View</a>
                </div>
            </div>
        @endforeach
    </div>
    @if(isset($products))
        <div style="margin-top:1rem;">
            {{ $products->links() }}
        </div>
    @endif
    <p style="margin-top:1rem;"><a href="/dashboard">← Back to Dashboard</a></p>

    <script>
        // Initialize Pusher
        Pusher.logToConsole = true;

        const pusher = new Pusher('aitechhubkey', {
            wsHost: window.location.hostname,
            wsPort: 6001,
            forceTLS: false,
            disableStats: true,
            enabledTransports: ['ws', 'wss']
        });

        // Subscribe to products channel
        const channel = pusher.subscribe('products');

        // Listen for new products
        channel.bind('product.created', function(data) {
            console.log('New product created:', data);
            addProductCard(data);
        });

        // Listen for stock updates
        channel.bind('product.stock.updated', function(data) {
            console.log('Product stock updated:', data);
            updateProductStock(data.id, data.stock);
        });

        function addProductCard(product) {
            const grid = document.getElementById('productsGrid');
            const card = document.createElement('div');
            card.className = 'card new-product';
            card.setAttribute('data-product-id', product.id);
            card.setAttribute('data-product-slug', product.slug);

            card.innerHTML = `
                ${product.image_url ? `<img src="${product.image_url}" alt="${product.name}">` : ''}
                <h3 style="margin:0.5rem 0;">${product.name}</h3>
                <div class="price">$${parseFloat(product.price).toFixed(2)}</div>
                <div class="stock stock-${product.id}"${product.stock < 5 ? ' style="color: #f56565; font-weight: 600;"' : ''}>
                    Stock: <span class="stock-value">${product.stock}</span>
                </div>
                <div style="margin-top:0.5rem;">
                    <a href="/products/${product.slug}" class="btn">View</a>
                </div>
            `;

            grid.insertBefore(card, grid.firstChild);

            // Remove animation class after animation completes
            setTimeout(() => card.classList.remove('new-product'), 500);
        }

        function updateProductStock(productId, newStock) {
            const stockElement = document.querySelector(`.stock-${productId} .stock-value`);
            const stockContainer = document.querySelector(`.stock-${productId}`);

            if (stockElement) {
                stockElement.textContent = newStock;

                // Update styling for low stock
                if (newStock < 5) {
                    stockContainer.style.color = '#f56565';
                    stockContainer.style.fontWeight = '600';
                    stockContainer.classList.add('low');
                } else {
                    stockContainer.style.color = '#718096';
                    stockContainer.style.fontWeight = 'normal';
                    stockContainer.classList.remove('low');
                }

                // Flash effect
                stockContainer.style.background = '#fef5e7';
                setTimeout(() => stockContainer.style.background = '', 1000);
            }
        }

        // Show loading overlay on form submit
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function() {
                document.getElementById('loadingOverlay').classList.add('active');
            });
        });
    </script>
</body>
</html>


