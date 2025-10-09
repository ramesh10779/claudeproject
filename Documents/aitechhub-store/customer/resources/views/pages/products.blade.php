@extends('layouts.app')

@section('title', 'Products - AITechHub Store')

@push('styles')
<style>
    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 2rem;
        margin-top: 2rem;
    }

    .product-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        transition: all 0.3s;
        text-decoration: none;
        color: inherit;
    }

    .product-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.15);
        border-color: #2a5298;
    }

    .product-image {
        width: 100%;
        height: 220px;
        object-fit: cover;
        background: #f7fafc;
    }

    .product-info {
        padding: 1.5rem;
    }

    .product-name {
        font-weight: 700;
        color: #1a202c;
        margin-bottom: 0.75rem;
        font-size: 1.1rem;
    }

    .product-price {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1e3c72;
        margin: 0.75rem 0;
    }

    .product-stock {
        font-size: 0.9rem;
        color: #718096;
        margin-top: 0.5rem;
    }

    .product-stock.low {
        color: #ef4444;
        font-weight: 600;
    }

    .product-rating {
        color: #fbbf24;
        margin-bottom: 0.5rem;
    }

    .filters-section {
        background: white;
        padding: 1.5rem;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        margin-bottom: 2rem;
    }

    .filters-form {
        display: flex;
        gap: 1rem;
        align-items: flex-end;
        flex-wrap: wrap;
    }

    .form-group {
        flex: 1;
        min-width: 200px;
    }

    .form-group label {
        display: block;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }

    .form-control {
        width: 100%;
        padding: 0.75rem;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        font-size: 1rem;
        transition: all 0.2s;
    }

    .form-control:focus {
        outline: none;
        border-color: #2a5298;
    }

    .btn {
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-block;
    }

    .btn-primary {
        background: #1e3c72;
        color: white;
    }

    .btn-primary:hover {
        background: #2a5298;
        transform: translateY(-2px);
    }

    .btn-secondary {
        background: #718096;
        color: white;
    }

    .btn-secondary:hover {
        background: #4a5568;
    }

    .btn-view {
        width: 100%;
        margin-top: 1rem;
        background: #1e3c72;
        color: white;
        padding: 0.75rem;
        border-radius: 8px;
        text-align: center;
        text-decoration: none;
        display: block;
        font-weight: 600;
        transition: all 0.2s;
    }

    .btn-view:hover {
        background: #2a5298;
        transform: translateY(-2px);
    }

    .page-header {
        margin-bottom: 2rem;
    }

    .page-title {
        font-size: 2.5rem;
        color: #1a202c;
        margin-bottom: 0.5rem;
    }

    .page-subtitle {
        color: #4a5568;
        font-size: 1.1rem;
    }

    .pagination {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
        margin-top: 3rem;
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: white;
        border-radius: 12px;
        border: 2px dashed #e2e8f0;
    }

    .empty-state-icon {
        font-size: 4rem;
        margin-bottom: 1rem;
    }

    .empty-state-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1a202c;
        margin-bottom: 0.5rem;
    }

    .empty-state-text {
        color: #718096;
    }
</style>
@endpush

@section('content')
<a href="/" class="back-button">
    ← Back to Home
</a>

<div class="page-header">
    <h1 class="page-title">🛍️ All Products</h1>
    <p class="page-subtitle">Discover our curated selection of premium tech products</p>
</div>

<!-- Filters -->
<div class="filters-section">
    <form class="filters-form" method="GET" action="/products">
        <div class="form-group">
            <label for="search">Search Products</label>
            <input
                type="text"
                id="search"
                name="q"
                class="form-control"
                placeholder="Search by name..."
                value="{{ request('q') }}"
            />
        </div>

        <div class="form-group">
            <label for="type">Product Type</label>
            <select id="type" name="type" class="form-control">
                <option value="">All Types</option>
                <option value="one_time" {{ request('type') === 'one_time' ? 'selected' : '' }}>One-Time Purchase</option>
                <option value="subscription" {{ request('type') === 'subscription' ? 'selected' : '' }}>Subscription</option>
            </select>
        </div>

        <div class="form-group">
            <label for="category">Category</label>
            <select id="category" name="category" class="form-control">
                <option value="">All Categories</option>
                @foreach(($categories ?? []) as $cat)
                    <option value="{{ $cat->slug }}" {{ request('category') === $cat->slug ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary">
                🔍 Filter
            </button>
        </div>

        <div class="form-group">
            <a href="/products" class="btn btn-secondary">
                Reset
            </a>
        </div>
    </form>
</div>

<!-- Products Grid -->
@if(($products ?? collect())->count() > 0)
    <div class="product-grid">
        @foreach($products as $product)
            <div class="product-card">
                <a href="/products/{{ $product->slug }}" style="text-decoration: none; color: inherit;">
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

                        <div class="product-stock {{ $product->stock < 10 ? 'low' : '' }}">
                            @if($product->stock > 0)
                                @if($product->stock < 10)
                                    ⚠️ Only {{ $product->stock }} left
                                @else
                                    ✓ In Stock ({{ $product->stock }})
                                @endif
                            @else
                                ✗ Out of Stock
                            @endif
                        </div>

                        <a href="/products/{{ $product->slug }}" class="btn-view">
                            View Details
                        </a>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    @if(isset($products))
        <div class="pagination">
            {{ $products->links() }}
        </div>
    @endif
@else
    <div class="empty-state">
        <div class="empty-state-icon">🔍</div>
        <div class="empty-state-title">No Products Found</div>
        <p class="empty-state-text">Try adjusting your filters or search terms</p>
        <a href="/products" class="btn btn-primary" style="margin-top: 1.5rem;">
            View All Products
        </a>
    </div>
@endif
@endsection
