<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'AITechHub Store - Your Tech Marketplace')</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f7fafc;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Header Styles */
        .header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .header-top {
            background: rgba(0,0,0,0.1);
            padding: 0.5rem 0;
            font-size: 0.85rem;
        }

        .header-top .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-main {
            padding: 1rem 0;
        }

        .header-main .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: white;
            font-size: 1.5rem;
            font-weight: 700;
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            background: white;
            color: #1e3c72;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .nav-menu {
            display: flex;
            gap: 2rem;
            list-style: none;
            align-items: center;
        }

        .nav-menu a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s;
            padding: 0.5rem 1rem;
            border-radius: 6px;
        }

        .nav-menu a:hover {
            background: rgba(255,255,255,0.1);
        }

        .cart-badge {
            position: relative;
            padding: 0.5rem 1.25rem;
            background: white;
            color: #1e3c72;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
        }

        .cart-badge:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .badge-count {
            background: #ef4444;
            color: white;
            border-radius: 50%;
            width: 22px;
            height: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 700;
        }

        /* Container */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            width: 100%;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 2rem 0;
        }

        /* Back Button */
        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: white;
            color: #1e3c72;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s;
            margin-bottom: 1.5rem;
        }

        .back-button:hover {
            border-color: #1e3c72;
            transform: translateX(-3px);
        }

        /* Footer Styles */
        .footer {
            background: #1a202c;
            color: white;
            padding: 3rem 0 1.5rem;
            margin-top: auto;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .footer-section h3 {
            margin-bottom: 1rem;
            font-size: 1.1rem;
        }

        .footer-section ul {
            list-style: none;
        }

        .footer-section ul li {
            margin-bottom: 0.75rem;
        }

        .footer-section a {
            color: #cbd5e0;
            text-decoration: none;
            transition: color 0.2s;
        }

        .footer-section a:hover {
            color: white;
        }

        .footer-bottom {
            border-top: 1px solid #2d3748;
            padding-top: 1.5rem;
            text-align: center;
            color: #a0aec0;
            font-size: 0.9rem;
        }

        .social-links {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }

        .social-links a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: #2d3748;
            border-radius: 50%;
            color: white;
            text-decoration: none;
            transition: all 0.2s;
        }

        .social-links a:hover {
            background: #4a5568;
            transform: translateY(-3px);
        }

        /* Success/Error Messages */
        .alert {
            padding: 1rem 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            animation: slideDown 0.3s ease-out;
        }

        .alert-success {
            background: #d1fae5;
            border: 2px solid #10b981;
            color: #065f46;
        }

        .alert-error {
            background: #fee2e2;
            border: 2px solid #ef4444;
            color: #991b1b;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .nav-menu {
                gap: 1rem;
                flex-wrap: wrap;
            }

            .header-main .container {
                flex-direction: column;
                gap: 1rem;
            }

            .container {
                padding: 0 1rem;
            }

            .footer-content {
                grid-template-columns: 1fr;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Header -->
    <header class="header">
        <!-- Top Bar -->
        <div class="header-top">
            <div class="container">
                <div>📧 support@aitechhub.store | 📞 +1-800-TECH-HUB</div>
                <div>Free Shipping on Orders Over $50!</div>
            </div>
        </div>

        <!-- Main Header -->
        <div class="header-main">
            <div class="container">
                <a href="/" class="logo">
                    <div class="logo-icon">🛒</div>
                    <span>AITechHub Store</span>
                </a>

                <nav>
                    <ul class="nav-menu">
                        <li><a href="/">Home</a></li>
                        <li><a href="/products">Products</a></li>
                        <li><a href="/products?type=subscription">Subscriptions</a></li>
                        <li><a href="/products?type=one_time">One-Time</a></li>

                        @php
                            $cart = session('cart', []);
                            $cartCount = collect($cart)->sum('quantity');
                        @endphp

                        <li>
                            <a href="{{ $cartCount > 0 ? (auth()->check() ? '/checkout' : '/login') : '/products' }}" class="cart-badge">
                                🛒 Cart
                                @if($cartCount > 0)
                                    <span class="badge-count">{{ $cartCount }}</span>
                                @endif
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            @if(session('success'))
                <div class="alert alert-success">
                    <span style="font-size: 1.5rem;">✓</span>
                    <strong>{{ session('success') }}</strong>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-error">
                    <span style="font-size: 1.5rem;">⚠</span>
                    <strong>{{ session('error') }}</strong>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>About AITechHub</h3>
                    <p style="color: #cbd5e0; line-height: 1.6;">
                        Your trusted marketplace for cutting-edge technology products. Quality guaranteed, fast shipping, excellent customer service.
                    </p>
                    <div class="social-links">
                        <a href="#" title="Facebook">📘</a>
                        <a href="#" title="Twitter">🐦</a>
                        <a href="#" title="Instagram">📷</a>
                        <a href="#" title="LinkedIn">💼</a>
                    </div>
                </div>

                <div class="footer-section">
                    <h3>Shop</h3>
                    <ul>
                        <li><a href="/products">All Products</a></li>
                        <li><a href="/products?type=one_time">One-Time Purchase</a></li>
                        <li><a href="/products?type=subscription">Subscriptions</a></li>
                        <li><a href="/products">Coupons & Deals</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h3>Customer Service</h3>
                    <ul>
                        <li><a href="#">Track Order</a></li>
                        <li><a href="#">Help Center</a></li>
                        <li><a href="#">Returns & Refunds</a></li>
                        <li><a href="#">Contact Us</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h3>Account</h3>
                    <ul>
                        @auth
                            <li><a href="/profile">My Profile</a></li>
                            <li><a href="/orders">My Orders</a></li>
                            <li><a href="/wishlist">My Wishlist</a></li>
                            <li><a href="/reviews">My Reviews</a></li>
                        @else
                            <li><a href="/login">Sign In</a></li>
                            <li><a href="/register">Create Account</a></li>
                        @endauth
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <p>© 2025 AITechHub Store. All rights reserved.</p>
                <p style="margin-top: 0.5rem;">
                    <a href="#" style="color: #a0aec0;">Privacy Policy</a> •
                    <a href="#" style="color: #a0aec0;">Terms of Service</a> •
                    <a href="#" style="color: #a0aec0;">Cookie Policy</a>
                </p>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
