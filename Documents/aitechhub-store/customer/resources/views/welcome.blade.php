@extends('layouts.app')

@section('title', 'AITechHub Store - Your Premier Tech Marketplace')

@push('styles')
<style>
    .hero-section {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        color: white;
        padding: 4rem 0;
        text-align: center;
        border-radius: 12px;
        margin-bottom: 3rem;
    }

    .hero-title {
        font-size: 3rem;
        font-weight: 700;
        margin-bottom: 1rem;
    }

    .hero-subtitle {
        font-size: 1.25rem;
        margin-bottom: 2rem;
        opacity: 0.95;
    }

    .cta-buttons {
        display: flex;
        gap: 1rem;
        justify-content: center;
        flex-wrap: wrap;
    }

    .btn {
        padding: 1rem 2.5rem;
        border-radius: 50px;
        font-size: 1.1rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        border: none;
        cursor: pointer;
    }

    .btn-primary {
        background: white;
        color: #1e3c72;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }

    .btn-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.3);
    }

    .btn-secondary {
        background: rgba(255,255,255,0.1);
        color: white;
        border: 2px solid white;
    }

    .btn-secondary:hover {
        background: rgba(255,255,255,0.2);
        transform: translateY(-3px);
    }

    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 2rem;
        margin: 3rem 0;
    }

    .feature-card {
        background: white;
        padding: 2rem;
        border-radius: 12px;
        border: 2px solid #e2e8f0;
        transition: all 0.3s;
        text-align: center;
    }

    .feature-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.1);
        border-color: #2a5298;
    }

    .feature-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
        display: block;
    }

    .feature-title {
        color: #1a202c;
        font-weight: 700;
        margin-bottom: 0.75rem;
        font-size: 1.25rem;
    }

    .feature-description {
        color: #4a5568;
        line-height: 1.6;
    }

    .products-highlight {
        margin: 4rem 0;
    }

    .section-title {
        text-align: center;
        font-size: 2.5rem;
        color: #1a202c;
        margin-bottom: 1rem;
    }

    .section-subtitle {
        text-align: center;
        color: #4a5568;
        font-size: 1.1rem;
        margin-bottom: 3rem;
    }

    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 2rem;
    }

    .product-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        transition: all 0.3s;
        text-decoration: none;
        color: inherit;
        display: flex;
        flex-direction: column;
    }

    .product-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.15);
    }

    .product-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
        background: #f7fafc;
    }

    .product-info {
        padding: 1.5rem;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .product-name {
        font-weight: 700;
        color: #1a202c;
        margin-bottom: 0.5rem;
        font-size: 1.1rem;
    }

    .product-price {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1e3c72;
        margin-top: auto;
    }

    .product-rating {
        color: #fbbf24;
        margin-bottom: 0.5rem;
    }

    .stats-section {
        background: white;
        padding: 3rem;
        border-radius: 12px;
        margin: 3rem 0;
        border: 1px solid #e2e8f0;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 2rem;
        text-align: center;
    }

    .stat-number {
        font-size: 3rem;
        font-weight: 700;
        color: #1e3c72;
        display: block;
    }

    .stat-label {
        color: #718096;
        font-size: 1rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-top: 0.5rem;
    }

    @media (max-width: 768px) {
        .hero-title {
            font-size: 2rem;
        }

        .hero-subtitle {
            font-size: 1rem;
        }

        .product-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <h1 class="hero-title">🚀 Welcome to AITechHub Store</h1>
    <p class="hero-subtitle">
        Your Premier Tech Marketplace - Discover, Shop, and Experience Innovation
    </p>
    <div class="cta-buttons">
        <a href="/products" class="btn btn-primary">
            <span>🛍️</span> Start Shopping
        </a>
        <a href="/products?type=subscription" class="btn btn-secondary">
            <span>📦</span> View Subscriptions
        </a>
    </div>
</section>

<!-- Stats Section -->
<div class="stats-section">
    <div class="stats-grid">
        <div class="stat-item">
            <span class="stat-number">500+</span>
            <span class="stat-label">Products</span>
        </div>
        <div class="stat-item">
            <span class="stat-number">10K+</span>
            <span class="stat-label">Happy Customers</span>
        </div>
        <div class="stat-item">
            <span class="stat-number">99.9%</span>
            <span class="stat-label">Uptime</span>
        </div>
        <div class="stat-item">
            <span class="stat-number">24/7</span>
            <span class="stat-label">Support</span>
        </div>
    </div>
</div>

<!-- Features Section -->
<section>
    <h2 class="section-title">Why Choose AITechHub?</h2>
    <p class="section-subtitle">Experience the best in tech shopping</p>

    <div class="features-grid">
        <div class="feature-card">
            <span class="feature-icon">🛍️</span>
            <div class="feature-title">Vast Product Catalog</div>
            <div class="feature-description">
                Browse thousands of tech products with advanced filtering and search capabilities
            </div>
        </div>

        <div class="feature-card">
            <span class="feature-icon">🔒</span>
            <div class="feature-title">Secure Checkout</div>
            <div class="feature-description">
                PCI DSS compliant payment processing with Stripe & PayPal integration
            </div>
        </div>

        <div class="feature-card">
            <span class="feature-icon">📦</span>
            <div class="feature-title">Fast Shipping</div>
            <div class="feature-description">
                Free shipping on orders over $50. Real-time order tracking included
            </div>
        </div>

        <div class="feature-card">
            <span class="feature-icon">⭐</span>
            <div class="feature-title">Verified Reviews</div>
            <div class="feature-description">
                Real customer reviews and ratings to help you make informed decisions
            </div>
        </div>

        <div class="feature-card">
            <span class="feature-icon">💝</span>
            <div class="feature-title">Wishlist & Favorites</div>
            <div class="feature-description">
                Save your favorite products and get notified about price drops
            </div>
        </div>

        <div class="feature-card">
            <span class="feature-icon">🎟️</span>
            <div class="feature-title">Exclusive Deals</div>
            <div class="feature-description">
                Access to exclusive discounts, coupons, and promotional offers
            </div>
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<section class="products-highlight">
    <h2 class="section-title">Featured Products</h2>
    <p class="section-subtitle">Handpicked items just for you</p>

    <div class="product-grid">
        @php
        $featuredProducts = \App\Models\Product::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();
        @endphp

        @foreach($featuredProducts as $product)
        <a href="/products/{{ $product->slug }}" class="product-card">
            @if($product->image_url)
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="product-image">
            @else
                <div class="product-image" style="display: flex; align-items: center; justify-content: center; font-size: 4rem;">
                    📦
                </div>
            @endif
            <div class="product-info">
                <div class="product-name">{{ $product->name }}</div>
                <div class="product-rating">⭐⭐⭐⭐⭐ (4.8)</div>
                <div class="product-price">${{ number_format($product->price, 2) }}</div>
            </div>
        </a>
        @endforeach
    </div>

    <div style="text-align: center; margin-top: 3rem;">
        <a href="/products" class="btn btn-primary">
            View All Products
        </a>
    </div>
</section>
@endsection
