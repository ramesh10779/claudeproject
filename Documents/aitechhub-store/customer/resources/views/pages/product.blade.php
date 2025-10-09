<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $product->name }} - Store</title>
    <style>
        body { font-family: system-ui, sans-serif; padding: 2rem; background: #f7fafc; margin: 0; }
        .container { max-width: 1200px; margin: 0 auto; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
        .breadcrumb { padding: 0.75rem; background: #fff; border-radius: 6px; font-size: 0.9rem; border: 1px solid #e2e8f0; }
        .breadcrumb a { color: #1e3c72; text-decoration: none; }
        .breadcrumb span { color: #718096; margin: 0 0.5rem; }
        .cart-badge { position: relative; padding: 0.5rem 1rem; background: #1e3c72; color: white; border-radius: 6px; text-decoration: none; font-weight: 600; }
        .cart-badge:hover { background: #2a5298; }
        .badge-count { position: absolute; top: -8px; right: -8px; background: #ef4444; color: white; border-radius: 50%; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 700; }
        .success-message { background: #d1fae5; border: 2px solid #10b981; color: #065f46; padding: 1rem 1.5rem; border-radius: 8px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem; animation: slideDown 0.3s ease-out; }
        @keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        .wrap { display: flex; gap: 3rem; align-items: flex-start; background: white; padding: 2rem; border-radius: 12px; border: 1px solid #e2e8f0; }
        .img { max-width: 500px; flex-shrink: 0; }
        .img img { width: 100%; border-radius: 12px; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); }
        .info { flex: 1; }
        .price { font-weight: 700; color: #1e3c72; font-size: 2rem; margin: 1rem 0; }
        .stock-badge { display: inline-block; padding: 0.375rem 0.75rem; background: #ecfdf5; color: #10b981; border-radius: 6px; font-size: 0.875rem; font-weight: 600; margin-bottom: 1rem; }
        .stock-badge.low { background: #fef3c7; color: #d97706; }
        .description { color: #4a5568; line-height: 1.8; margin: 1.5rem 0; font-size: 1.05rem; }
        .add-to-cart-form { margin-top: 2rem; padding: 1.5rem; background: #f7fafc; border-radius: 8px; }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; font-weight: 600; color: #2d3748; margin-bottom: 0.5rem; }
        input[type=number] { padding: 0.75rem; width: 100px; border: 2px solid #e2e8f0; border-radius: 6px; font-size: 1rem; }
        .btn { display: inline-block; padding: 0.875rem 2rem; border-radius: 8px; background: #1e3c72; color: #fff; border: none; cursor: pointer; font-weight: 600; font-size: 1rem; transition: all 0.2s; }
        .btn:hover { background: #2a5298; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(30, 60, 114, 0.3); }
        .btn:disabled { background: #cbd5e0; cursor: not-allowed; transform: none; }
        @media (max-width: 768px) {
            .wrap { flex-direction: column; }
            .img { max-width: 100%; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="breadcrumb">
                <a href="/products">🛍️ Products</a>
                <span>›</span>
                <strong>{{ $product->name }}</strong>
            </div>

            @php
                $cart = session('cart', []);
                $cartCount = collect($cart)->sum('quantity');
            @endphp

            <a href="{{ $cartCount > 0 ? (auth()->check() ? '/checkout' : '/login') : '/products' }}" class="cart-badge">
                🛒 Cart
                @if($cartCount > 0)
                    <span class="badge-count">{{ $cartCount }}</span>
                @endif
            </a>
        </div>

        @if(session('success'))
            <div class="success-message">
                <span style="font-size: 1.5rem;">✓</span>
                <strong>{{ session('success') }}</strong>
            </div>
        @endif

        <div class="wrap">
            <div class="img">
                @if($product->image_url)
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" />
                @else
                    <div style="width: 100%; height: 400px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 4rem;">
                        📦
                    </div>
                @endif
            </div>
            <div class="info">
                <h1 style="margin: 0 0 0.5rem 0; color: #1a202c; font-size: 2.25rem;">{{ $product->name }}</h1>

                @if($product->stock > 0)
                    <span class="stock-badge {{ $product->stock < 10 ? 'low' : '' }}">
                        @if($product->stock < 10)
                            ⚠️ Only {{ $product->stock }} left in stock
                        @else
                            ✓ In Stock ({{ $product->stock }} available)
                        @endif
                    </span>
                @else
                    <span class="stock-badge" style="background: #fee2e2; color: #dc2626;">✗ Out of Stock</span>
                @endif

                <div class="price">${{ number_format($product->price, 2) }}</div>

                <p class="description">{{ $product->description ?? 'No description available.' }}</p>

                <div class="add-to-cart-form">
                    <form method="POST" action="/cart/add">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}" />

                        <div class="form-group">
                            <label for="quantity">Quantity:</label>
                            <input type="number" id="quantity" name="quantity" value="1" min="1" max="{{ $product->stock }}" {{ $product->stock == 0 ? 'disabled' : '' }} />
                        </div>

                        <button type="submit" class="btn" {{ $product->stock == 0 ? 'disabled' : '' }}>
                            @if($product->stock > 0)
                                🛒 Add to Cart
                            @else
                                Out of Stock
                            @endif
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
