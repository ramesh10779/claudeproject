<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AITechHub Store - Your Premier Tech Marketplace</title>
    <link rel="stylesheet" href="/css/customer-styles.css">
    <style>
        /* Hero Banner Styles */
        .hero-banner {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 4rem 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .hero-banner::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120"><path fill="%23ffffff" fill-opacity="0.1" d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z"></path></svg>') no-repeat center bottom;
            background-size: cover;
            opacity: 0.3;
        }

        .hero-content {
            position: relative;
            z-index: 1;
            max-width: 800px;
            margin: 0 auto;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
            text-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }

        .hero-subtitle {
            font-size: 1.3rem;
            margin-bottom: 2rem;
            opacity: 0.95;
        }

        .hero-search {
            max-width: 600px;
            margin: 2rem auto;
            position: relative;
        }

        .search-input {
            width: 100%;
            padding: 1.2rem 3.5rem 1.2rem 1.5rem;
            border-radius: 50px;
            border: none;
            font-size: 1.1rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .search-button {
            position: absolute;
            right: 0.5rem;
            top: 50%;
            transform: translateY(-50%);
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 50px;
            cursor: pointer;
            font-weight: 600;
        }

        .search-results {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border-radius: 12px;
            margin-top: 0.5rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            max-height: 400px;
            overflow-y: auto;
            display: none;
            z-index: 1000;
        }

        .search-results.active {
            display: block;
        }

        .search-result-item {
            padding: 1rem;
            border-bottom: 1px solid #e2e8f0;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 1rem;
            color: #2d3748;
            text-decoration: none;
        }

        .search-result-item:hover {
            background: #f7fafc;
        }

        .search-result-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
        }

        .search-result-info {
            flex: 1;
        }

        .search-result-name {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .search-result-price {
            color: #2a5298;
            font-weight: 700;
        }

        /* Categories Section */
        .categories-section {
            padding: 3rem 2rem;
            background: #f7fafc;
        }

        .section-title {
            text-align: center;
            font-size: 2.5rem;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 3rem;
        }

        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .category-card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            border: 2px solid transparent;
            text-decoration: none;
            color: inherit;
        }

        .category-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(30, 60, 114, 0.15);
            border-color: #2a5298;
        }

        .category-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            display: block;
        }

        .category-name {
            font-weight: 600;
            color: #2d3748;
            font-size: 1.1rem;
        }

        .category-count {
            color: #718096;
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }

        /* Featured Products Section */
        .featured-section {
            padding: 4rem 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .product-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(30, 60, 114, 0.2);
        }

        .product-image {
            width: 100%;
            height: 220px;
            object-fit: cover;
            background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
        }

        .product-info {
            padding: 1.5rem;
        }

        .product-name {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 0.5rem;
            font-size: 1.1rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-price {
            color: #2a5298;
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .product-rating {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .stars {
            color: #f6ad55;
        }

        .add-to-cart-btn {
            width: 100%;
            padding: 0.8rem;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .add-to-cart-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(30, 60, 114, 0.3);
        }

        /* Testimonials Section */
        .testimonials-section {
            padding: 4rem 2rem;
            background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
        }

        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 2rem;
            max-width: 1200px;
            margin: 2rem auto 0;
        }

        .testimonial-card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            position: relative;
        }

        .testimonial-quote {
            font-size: 3rem;
            color: #2a5298;
            opacity: 0.2;
            position: absolute;
            top: 1rem;
            left: 1rem;
        }

        .testimonial-text {
            color: #4a5568;
            line-height: 1.8;
            margin-bottom: 1.5rem;
            position: relative;
            z-index: 1;
        }

        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .author-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1.2rem;
        }

        .author-info {
            flex: 1;
        }

        .author-name {
            font-weight: 600;
            color: #2d3748;
        }

        .author-role {
            color: #718096;
            font-size: 0.9rem;
        }

        /* Newsletter Section */
        .newsletter-section {
            padding: 4rem 2rem;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            text-align: center;
        }

        .newsletter-content {
            max-width: 600px;
            margin: 0 auto;
        }

        .newsletter-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .newsletter-subtitle {
            font-size: 1.1rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }

        .newsletter-form {
            display: flex;
            gap: 1rem;
            max-width: 500px;
            margin: 0 auto;
        }

        .newsletter-input {
            flex: 1;
            padding: 1rem 1.5rem;
            border-radius: 50px;
            border: none;
            font-size: 1rem;
        }

        .newsletter-button {
            padding: 1rem 2.5rem;
            background: white;
            color: #1e3c72;
            border: none;
            border-radius: 50px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .newsletter-button:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        /* CTA Section */
        .cta-section {
            padding: 3rem 2rem;
            text-align: center;
        }

        .cta-buttons {
            display: flex;
            gap: 1.5rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .cta-button {
            padding: 1.2rem 3rem;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .primary-cta {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            border: none;
            box-shadow: 0 8px 20px rgba(30, 60, 114, 0.3);
        }

        .primary-cta:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(30, 60, 114, 0.4);
        }

        .secondary-cta {
            background: white;
            color: #1e3c72;
            border: 2px solid #1e3c72;
        }

        .secondary-cta:hover {
            background: #1e3c72;
            color: white;
            transform: translateY(-3px);
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2rem;
            }

            .newsletter-form {
                flex-direction: column;
            }

            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            }
        }
    </style>
</head>
<body>
    <!-- Hero Banner with Search -->
    <section class="hero-banner">
        <div class="hero-content">
            <h1 class="hero-title">🚀 AITechHub Store</h1>
            <p class="hero-subtitle">Discover the Latest Tech Products & Innovation</p>

            <!-- Live Product Search -->
            <div class="hero-search">
                <input
                    type="text"
                    class="search-input"
                    id="productSearch"
                    placeholder="🔍 Search for products, categories, brands..."
                    autocomplete="off"
                >
                <button class="search-button">Search</button>
                <div class="search-results" id="searchResults"></div>
            </div>
        </div>
    </section>

    <!-- Categories Quick Links -->
    <section class="categories-section">
        <h2 class="section-title">Shop by Category</h2>
        <div class="categories-grid">
            <a href="/products?category=laptops" class="category-card">
                <span class="category-icon">💻</span>
                <div class="category-name">Laptops</div>
                <div class="category-count">250+ Products</div>
            </a>
            <a href="/products?category=smartphones" class="category-card">
                <span class="category-icon">📱</span>
                <div class="category-name">Smartphones</div>
                <div class="category-count">180+ Products</div>
            </a>
            <a href="/products?category=tablets" class="category-card">
                <span class="category-icon">📲</span>
                <div class="category-name">Tablets</div>
                <div class="category-count">120+ Products</div>
            </a>
            <a href="/products?category=accessories" class="category-card">
                <span class="category-icon">🎧</span>
                <div class="category-name">Accessories</div>
                <div class="category-count">500+ Products</div>
            </a>
            <a href="/products?category=gaming" class="category-card">
                <span class="category-icon">🎮</span>
                <div class="category-name">Gaming</div>
                <div class="category-count">300+ Products</div>
            </a>
            <a href="/products?category=smart-home" class="category-card">
                <span class="category-icon">🏠</span>
                <div class="category-name">Smart Home</div>
                <div class="category-count">200+ Products</div>
            </a>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="featured-section">
        <h2 class="section-title">⭐ Featured Products</h2>
        <div class="products-grid" id="featuredProducts">
            <!-- Products will be loaded via JavaScript -->
            <div class="product-card">
                <img src="https://via.placeholder.com/280x220/2a5298/ffffff?text=Product+1" alt="Product" class="product-image">
                <div class="product-info">
                    <div class="product-name">Premium Wireless Headphones</div>
                    <div class="product-rating">
                        <span class="stars">⭐⭐⭐⭐⭐</span>
                        <span>(4.8)</span>
                    </div>
                    <div class="product-price">$299.99</div>
                    <button class="add-to-cart-btn">Add to Cart</button>
                </div>
            </div>

            <div class="product-card">
                <img src="https://via.placeholder.com/280x220/1e3c72/ffffff?text=Product+2" alt="Product" class="product-image">
                <div class="product-info">
                    <div class="product-name">Smart Watch Pro Series 7</div>
                    <div class="product-rating">
                        <span class="stars">⭐⭐⭐⭐⭐</span>
                        <span>(4.9)</span>
                    </div>
                    <div class="product-price">$399.99</div>
                    <button class="add-to-cart-btn">Add to Cart</button>
                </div>
            </div>

            <div class="product-card">
                <img src="https://via.placeholder.com/280x220/2a5298/ffffff?text=Product+3" alt="Product" class="product-image">
                <div class="product-info">
                    <div class="product-name">4K Ultra HD Camera</div>
                    <div class="product-rating">
                        <span class="stars">⭐⭐⭐⭐☆</span>
                        <span>(4.6)</span>
                    </div>
                    <div class="product-price">$799.99</div>
                    <button class="add-to-cart-btn">Add to Cart</button>
                </div>
            </div>

            <div class="product-card">
                <img src="https://via.placeholder.com/280x220/1e3c72/ffffff?text=Product+4" alt="Product" class="product-image">
                <div class="product-info">
                    <div class="product-name">Mechanical Gaming Keyboard RGB</div>
                    <div class="product-rating">
                        <span class="stars">⭐⭐⭐⭐⭐</span>
                        <span>(4.7)</span>
                    </div>
                    <div class="product-price">$149.99</div>
                    <button class="add-to-cart-btn">Add to Cart</button>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="testimonials-section">
        <h2 class="section-title">💬 What Our Customers Say</h2>
        <div class="testimonials-grid">
            <div class="testimonial-card">
                <div class="testimonial-quote">"</div>
                <p class="testimonial-text">
                    "Amazing products and fast delivery! I bought a laptop and it arrived in perfect condition. The customer service is top-notch!"
                </p>
                <div class="testimonial-author">
                    <div class="author-avatar">JS</div>
                    <div class="author-info">
                        <div class="author-name">John Smith</div>
                        <div class="author-role">Software Engineer</div>
                    </div>
                </div>
            </div>

            <div class="testimonial-card">
                <div class="testimonial-quote">"</div>
                <p class="testimonial-text">
                    "Best tech store online! Great prices, authentic products, and excellent support. Highly recommend for all your tech needs!"
                </p>
                <div class="testimonial-author">
                    <div class="author-avatar">MJ</div>
                    <div class="author-info">
                        <div class="author-name">Maria Johnson</div>
                        <div class="author-role">Product Manager</div>
                    </div>
                </div>
            </div>

            <div class="testimonial-card">
                <div class="testimonial-quote">"</div>
                <p class="testimonial-text">
                    "I've been a customer for 2 years now. Never disappointed! Always genuine products and competitive prices. Love it!"
                </p>
                <div class="testimonial-author">
                    <div class="author-avatar">DL</div>
                    <div class="author-info">
                        <div class="author-name">David Lee</div>
                        <div class="author-role">Tech Enthusiast</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Newsletter Signup -->
    <section class="newsletter-section">
        <div class="newsletter-content">
            <h2 class="newsletter-title">📧 Stay Updated!</h2>
            <p class="newsletter-subtitle">
                Subscribe to our newsletter for exclusive deals, new product launches, and tech news!
            </p>
            <form class="newsletter-form" id="newsletterForm">
                <input
                    type="email"
                    class="newsletter-input"
                    placeholder="Enter your email address"
                    required
                >
                <button type="submit" class="newsletter-button">Subscribe</button>
            </form>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="cta-section">
        <h2 class="section-title">Ready to Start Shopping?</h2>
        <div class="cta-buttons">
            <a href="/register" class="cta-button primary-cta">
                <span>🚀</span> Create Account
            </a>
            <a href="/login" class="cta-button secondary-cta">
                <span>🔑</span> Sign In
            </a>
        </div>
    </section>

    <!-- Footer -->
    @include('components.footer')

    <script>
        // Live Product Search
        const searchInput = document.getElementById('productSearch');
        const searchResults = document.getElementById('searchResults');
        let searchTimeout;

        searchInput.addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            const query = e.target.value.trim();

            if (query.length < 2) {
                searchResults.classList.remove('active');
                return;
            }

            searchTimeout = setTimeout(async () => {
                // Real-time product search via API
                try {
                    const response = await fetch(`/api/search/products?q=${encodeURIComponent(query)}`);
                    const products = await response.json();
                    displaySearchResults(products);
                } catch (error) {
                    console.error('Search error:', error);
                    searchResults.innerHTML = '<div style="padding: 1rem; text-align: center; color: #f56565;">Search failed. Please try again.</div>';
                    searchResults.classList.add('active');
                }
            }, 300);
        });

        function displaySearchResults(results) {
            if (results.length === 0) {
                searchResults.innerHTML = '<div style="padding: 1rem; text-align: center; color: #718096;">No products found</div>';
                searchResults.classList.add('active');
                return;
            }

            searchResults.innerHTML = results.map(product => `
                <a href="${product.url}" class="search-result-item">
                    <img src="${product.image}" alt="${product.name}" class="search-result-image">
                    <div class="search-result-info">
                        <div class="search-result-name">${product.name}</div>
                        <div class="search-result-price">$${product.price}</div>
                    </div>
                </a>
            `).join('');

            searchResults.classList.add('active');
        }

        // Close search results when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.hero-search')) {
                searchResults.classList.remove('active');
            }
        });

        // Newsletter Form
        document.getElementById('newsletterForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const email = this.querySelector('input').value;

            // Simulated API call - Replace with actual endpoint
            alert(`Thank you for subscribing! We'll send updates to ${email}`);
            this.reset();
        });

        // Add to Cart buttons (placeholder functionality)
        document.querySelectorAll('.add-to-cart-btn').forEach(button => {
            button.addEventListener('click', function() {
                // Check if user is logged in - Replace with actual auth check
                const isLoggedIn = false; // This should come from your auth system

                if (!isLoggedIn) {
                    if (confirm('Please login to add items to cart. Go to login page?')) {
                        window.location.href = '/login';
                    }
                } else {
                    // Add to cart logic here
                    alert('Product added to cart!');
                    this.textContent = '✓ Added';
                    this.style.background = '#48bb78';
                    setTimeout(() => {
                        this.textContent = 'Add to Cart';
                        this.style.background = '';
                    }, 2000);
                }
            });
        });
    </script>
</body>
</html>
