<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Enhanced Product Management - AITechHub Admin</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', -apple-system, system-ui, sans-serif; background: #f7fafc; color: #1a202c; }
        .container { max-width: 1600px; margin: 0 auto; padding: 2rem; }
        
        /* Header */
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 2rem; border-radius: 12px; margin-bottom: 2rem; box-shadow: 0 4px 20px rgba(102, 126, 234, 0.3); }
        .header h1 { font-size: 2rem; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 1rem; }
        .header-actions { display: flex; gap: 1rem; margin-top: 1.5rem; flex-wrap: wrap; }
        
        /* Buttons */
        .btn { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; text-decoration: none; border: none; cursor: pointer; transition: all 0.3s; font-size: 0.95rem; }
        .btn-primary { background: white; color: #667eea; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
        .btn-secondary { background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3); }
        .btn-secondary:hover { background: rgba(255,255,255,0.3); }
        .btn-success { background: #48bb78; color: white; }
        .btn-danger { background: #f56565; color: white; }
        .btn-outline { background: white; color: #667eea; border: 2px solid #667eea; }
        .btn-sm { padding: 0.5rem 1rem; font-size: 0.85rem; }
        
        /* Stats */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
        .stat-card { background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .stat-label { font-size: 0.85rem; color: #718096; margin-bottom: 0.5rem; }
        .stat-value { font-size: 2rem; font-weight: 700; color: #1a202c; }
        
        /* Card */
        .card { background: white; border-radius: 12px; padding: 2rem; margin-bottom: 2rem; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        
        /* Filters */
        .filters { display: flex; gap: 1rem; margin-bottom: 2rem; flex-wrap: wrap; }
        .search-box { flex: 1; min-width: 250px; }
        .search-box input, select { width: 100%; padding: 0.75rem 1rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 1rem; }
        .search-box input:focus, select:focus { outline: none; border-color: #667eea; }
        
        /* Table */
        table { width: 100%; border-collapse: collapse; }
        th { background: #f7fafc; padding: 1rem; text-align: left; font-weight: 600; color: #4a5568; border-bottom: 2px solid #e2e8f0; }
        td { padding: 1rem; border-bottom: 1px solid #e2e8f0; }
        tr:hover { background: #f7fafc; }
        .product-image { width: 50px; height: 50px; object-fit: cover; border-radius: 6px; }
        
        /* Badge */
        .badge { display: inline-block; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.85rem; font-weight: 600; }
        .badge-success { background: #c6f6d5; color: #22543d; }
        .badge-danger { background: #fed7d7; color: #742a2a; }
        .badge-warning { background: #feebc8; color: #744210; }
        
        /* Actions */
        .actions { display: flex; gap: 0.5rem; }
        .bulk-actions { display: flex; gap: 1rem; align-items: center; padding: 1rem; background: #f7fafc; border-radius: 8px; margin-bottom: 1rem; }
        .bulk-actions.hidden { display: none; }
        .checkbox { width: 18px; height: 18px; cursor: pointer; }
        
        /* Modal */
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; overflow-y: auto; }
        .modal.active { display: flex; align-items: center; justify-content: center; padding: 2rem; }
        .modal-content { background: white; border-radius: 12px; padding: 2rem; max-width: 900px; width: 100%; max-height: 90vh; overflow-y: auto; }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
        .modal-header h2 { font-size: 1.5rem; color: #1a202c; }
        .close-modal { background: none; border: none; font-size: 2rem; color: #718096; cursor: pointer; line-height: 1; }
        
        /* Form */
        .form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; }
        .form-group { margin-bottom: 1.5rem; }
        .form-group.full-width { grid-column: 1 / -1; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 600; color: #4a5568; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 1rem; font-family: inherit; }
        .form-group textarea { min-height: 100px; resize: vertical; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: #667eea; }
        .checkbox-group { display: flex; align-items: center; gap: 0.5rem; }
        .checkbox-group input[type="checkbox"] { width: auto; }
        
        /* Alert */
        .alert { padding: 1rem; border-radius: 8px; margin-bottom: 1rem; }
        .alert-success { background: #c6f6d5; color: #22543d; border: 1px solid #9ae6b4; }
        .alert-error { background: #fed7d7; color: #742a2a; border: 1px solid #fc8181; }
        
        /* Upload Area */
        .upload-area { border: 2px dashed #e2e8f0; border-radius: 8px; padding: 2rem; text-align: center; cursor: pointer; transition: all 0.3s; }
        .upload-area:hover { border-color: #667eea; background: #f7fafc; }
        .upload-area.dragover { border-color: #667eea; background: #e6f2ff; }
        .file-input { display: none; }
        
        /* Pagination */
        .pagination { display: flex; gap: 0.5rem; justify-content: center; margin-top: 2rem; }
        .pagination a, .pagination span { padding: 0.5rem 1rem; border: 1px solid #e2e8f0; border-radius: 6px; text-decoration: none; color: #4a5568; }
        .pagination a:hover { background: #667eea; color: white; border-color: #667eea; }
        .pagination .active { background: #667eea; color: white; border-color: #667eea; }
        
        /* Tabs */
        .tabs { display: flex; gap: 1rem; margin-bottom: 1.5rem; border-bottom: 2px solid #e2e8f0; }
        .tab { padding: 1rem 1.5rem; cursor: pointer; border-bottom: 2px solid transparent; margin-bottom: -2px; transition: all 0.3s; }
        .tab.active { border-bottom-color: #667eea; color: #667eea; font-weight: 600; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        
        /* Loading */
        .loading { display: inline-block; width: 20px; height: 20px; border: 3px solid #f3f3f3; border-top: 3px solid #667eea; border-radius: 50%; animation: spin 1s linear infinite; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
</head>
<body>
    @include('components.navbar', ['title' => '📦 Enhanced Upload & Bulk Management', 'backUrl' => route('admin.products')])

    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1><span>📦</span> Enhanced Upload & Bulk Management</h1>
            <p style="opacity: 0.9;">Complete product catalog management with bulk operations, import/export, and advanced features</p>
            <div class="header-actions">
                <button class="btn btn-primary" onclick="openBulkImportModal()"><span>📊</span> Bulk Import</button>
                <button class="btn btn-primary" onclick="exportProducts()"><span>📥</span> Export Products</button>
                <form method="POST" action="{{ route('admin.products.seed') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-primary"><span>🌱</span> Seed Demo Products</button>
                </form>
                <form method="POST" action="{{ route('admin.products.clear') }}" onsubmit="return confirm('Are you sure you want to clear all products? This action cannot be undone.');" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-danger"><span>🗑️</span> Clear All</button>
                </form>
            </div>
        </div>

        @if(session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Total Products</div>
                <div class="stat-value">{{ $products['total'] ?? 0 }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Active Products</div>
                <div class="stat-value" id="activeCount">-</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Out of Stock</div>
                <div class="stat-value" id="outOfStockCount">-</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total Inventory Value</div>
                <div class="stat-value" id="totalValue">$0</div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card">
            <div class="filters">
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="🔍 Search products by name, SKU, or shortcode..." value="{{ request('search') }}">
                </div>
                <select id="categoryFilter" onchange="applyFilters()">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat['id'] ?? $cat['slug'] ?? '' }}" {{ request('category') == ($cat['id'] ?? $cat['slug'] ?? '') ? 'selected' : '' }}>{{ $cat['name'] ?? 'Unknown' }}</option>
                    @endforeach
                </select>
                <select id="statusFilter" onchange="applyFilters()">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                <button class="btn btn-outline" onclick="clearFilters()">Clear Filters</button>
            </div>

            <!-- Bulk Actions -->
            <div class="bulk-actions hidden" id="bulkActions">
                <input type="checkbox" class="checkbox" id="selectAll" onchange="toggleSelectAll()">
                <span id="selectedCount">0 selected</span>
                <button class="btn btn-sm btn-success" onclick="bulkActivate()">Activate</button>
                <button class="btn btn-sm btn-danger" onclick="bulkDeactivate()">Deactivate</button>
                <button class="btn btn-sm btn-danger" onclick="bulkDelete()">Delete</button>
                <button class="btn btn-sm btn-outline" onclick="openBulkEditModal()">Bulk Edit</button>
            </div>

            <!-- Products Table -->
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th><input type="checkbox" class="checkbox" id="selectAllHeader" onchange="toggleSelectAll()"></th>
                            <th>Image</th>
                            <th>Product Name</th>
                            <th>SKU</th>
                            <th>Price</th>
                            <th>Sale Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="productsTable">
                        @forelse($products['data'] ?? [] as $product)
                            <tr data-product-id="{{ $product['id'] }}">
                                <td><input type="checkbox" class="checkbox product-checkbox" value="{{ $product['id'] }}" onchange="updateBulkActions()"></td>
                                <td>
                                    @if($product['image_url'])
                                        <img src="{{ $product['image_url'] }}" alt="{{ $product['name'] }}" class="product-image">
                                    @else
                                        <div style="width: 50px; height: 50px; background: #e2e8f0; border-radius: 6px; display: flex; align-items: center; justify-content: center;">📦</div>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $product['name'] }}</strong>
                                    @if($product['is_featured'] ?? false)
                                        <span class="badge badge-warning">⭐ Featured</span>
                                    @endif
                                </td>
                                <td>{{ $product['sku'] ?? 'N/A' }}</td>
                                <td>${{ number_format($product['price'] ?? 0, 2) }}</td>
                                <td>
                                    @if($product['sale_price'])
                                        <span style="color: #e53e3e; font-weight: 600;">${{ number_format($product['sale_price'], 2) }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($product['stock'] > 10)
                                        <span class="badge badge-success">{{ $product['stock'] }}</span>
                                    @elseif($product['stock'] > 0)
                                        <span class="badge badge-warning">{{ $product['stock'] }}</span>
                                    @else
                                        <span class="badge badge-danger">Out of Stock</span>
                                    @endif
                                </td>
                                <td>
                                    @if($product['is_active'])
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="actions">
                                        <button class="btn btn-sm btn-outline" onclick='editProduct(@json($product))'>Edit</button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteProduct({{ $product['id'] }})">Delete</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" style="text-align: center; padding: 3rem;">
                                    <div style="font-size: 3rem; margin-bottom: 1rem;">📦</div>
                                    <p>No products found. Add your first product to get started!</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if(isset($products['last_page']) && $products['last_page'] > 1)
                <div class="pagination">
                    @if($products['current_page'] > 1)
                        <a href="?page={{ $products['current_page'] - 1 }}">← Previous</a>
                    @endif

                    @for($i = 1; $i <= $products['last_page']; $i++)
                        @if($i == $products['current_page'])
                            <span class="active">{{ $i }}</span>
                        @else
                            <a href="?page={{ $i }}">{{ $i }}</a>
                        @endif
                    @endfor

                    @if($products['current_page'] < $products['last_page'])
                        <a href="?page={{ $products['current_page'] + 1 }}">Next →</a>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Add/Edit Product Modal -->
    <div class="modal" id="productModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Add New Product</h2>
                <button class="close-modal" onclick="closeProductModal()">&times;</button>
            </div>
            <form id="productForm" onsubmit="saveProduct(event)">
                <input type="hidden" id="productId" name="id">

                <div class="form-grid">
                    <div class="form-group full-width">
                        <label for="name">Product Name *</label>
                        <input type="text" id="name" name="name" required>
                    </div>

                    <div class="form-group">
                        <label for="sku">SKU</label>
                        <input type="text" id="sku" name="sku" placeholder="Auto-generated if empty">
                    </div>

                    <div class="form-group">
                        <label for="shortcode">Shortcode</label>
                        <input type="text" id="shortcode" name="shortcode" placeholder="Auto-generated if empty">
                    </div>

                    <div class="form-group">
                        <label for="price">Price *</label>
                        <input type="number" id="price" name="price" step="0.01" min="0" required>
                    </div>

                    <div class="form-group">
                        <label for="sale_price">Sale Price</label>
                        <input type="number" id="sale_price" name="sale_price" step="0.01" min="0">
                    </div>

                    <div class="form-group">
                        <label for="cost_price">Cost Price</label>
                        <input type="number" id="cost_price" name="cost_price" step="0.01" min="0">
                    </div>

                    <div class="form-group">
                        <label for="stock">Stock Quantity *</label>
                        <input type="number" id="stock" name="stock" min="0" required>
                    </div>

                    <div class="form-group">
                        <label for="product_type">Product Type *</label>
                        <select id="product_type" name="product_type" required>
                            <option value="one_time">One-Time Purchase</option>
                            <option value="subscription">Subscription</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="category_id">Category</label>
                        <select id="category_id" name="category_id">
                            <option value="">Select Category</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat['id'] }}">{{ $cat['name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group full-width">
                        <label for="image_url">Primary Image URL</label>
                        <input type="url" id="image_url" name="image_url" placeholder="https://example.com/image.jpg">
                    </div>

                    <div class="form-group full-width">
                        <label for="short_description">Short Description</label>
                        <textarea id="short_description" name="short_description" rows="3"></textarea>
                    </div>

                    <div class="form-group full-width">
                        <label for="description">Full Description</label>
                        <textarea id="description" name="description" rows="5"></textarea>
                    </div>

                    <div class="form-group full-width">
                        <label for="specifications">Specifications (JSON format)</label>
                        <textarea id="specifications" name="specifications" rows="3" placeholder='{"weight": "1kg", "dimensions": "10x10x10cm"}'></textarea>
                    </div>

                    <div class="form-group">
                        <div class="checkbox-group">
                            <input type="checkbox" id="is_featured" name="is_featured" value="1">
                            <label for="is_featured">Featured Product</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="checkbox-group">
                            <input type="checkbox" id="is_active" name="is_active" value="1" checked>
                            <label for="is_active">Active</label>
                        </div>
                    </div>
                </div>

                <div style="margin-top: 2rem; display: flex; gap: 1rem; justify-content: flex-end;">
                    <button type="button" class="btn btn-outline" onclick="closeProductModal()">Cancel</button>
                    <button type="submit" class="btn btn-success" id="saveBtn">
                        <span class="loading" style="display: none;"></span>
                        <span class="btn-text">Save Product</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bulk Import Modal -->
    <div class="modal" id="bulkImportModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Bulk Import Products</h2>
                <button class="close-modal" onclick="closeBulkImportModal()">&times;</button>
            </div>

            <div class="tabs">
                <div class="tab active" onclick="showImportTab('csv')">CSV Import</div>
                <div class="tab" onclick="showImportTab('google')">Google Sheets</div>
            </div>

            <!-- CSV Import -->
            <div class="tab-content active" id="csvImport">
                <div class="upload-area" id="uploadArea" onclick="document.getElementById('fileInput').click()">
                    <div style="font-size: 3rem; margin-bottom: 1rem;">📁</div>
                    <p style="font-weight: 600; margin-bottom: 0.5rem;">Click to upload or drag and drop</p>
                    <p style="color: #718096; font-size: 0.9rem;">CSV files only</p>
                    <input type="file" id="fileInput" class="file-input" accept=".csv" onchange="handleFileSelect(event)">
                </div>
                <div id="fileInfo" style="margin-top: 1rem; display: none;">
                    <p><strong>Selected file:</strong> <span id="fileName"></span></p>
                    <button class="btn btn-success" onclick="uploadFile()">Upload and Import</button>
                </div>
                <div style="margin-top: 2rem;">
                    <a href="http://localhost:8000/api/admin/products/import-template" class="btn btn-outline" download>📥 Download Template</a>
                </div>
            </div>

            <!-- Google Sheets Import -->
            <div class="tab-content" id="googleImport">
                <div class="form-group">
                    <label for="googleSheetUrl">Google Sheets URL</label>
                    <input type="url" id="googleSheetUrl" placeholder="https://docs.google.com/spreadsheets/d/...">
                </div>
                <div class="form-group">
                    <label for="sheetName">Sheet Name (optional)</label>
                    <input type="text" id="sheetName" placeholder="Sheet1">
                </div>
                <button class="btn btn-success" onclick="importFromGoogleSheets()">Import from Google Sheets</button>
                <div style="margin-top: 1rem; padding: 1rem; background: #f7fafc; border-radius: 8px;">
                    <p style="font-size: 0.9rem; color: #4a5568;"><strong>Note:</strong> Make sure your Google Sheet is publicly accessible.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Edit Modal -->
    <div class="modal" id="bulkEditModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Bulk Edit Products</h2>
                <button class="close-modal" onclick="closeBulkEditModal()">&times;</button>
            </div>
            <form id="bulkEditForm" onsubmit="saveBulkEdit(event)">
                <p style="margin-bottom: 1.5rem; color: #718096;">Editing <strong id="bulkEditCount">0</strong> selected products</p>

                <div class="form-group">
                    <label for="bulk_price_type">Price Adjustment</label>
                    <select id="bulk_price_type">
                        <option value="">No Change</option>
                        <option value="increase_percent">Increase by %</option>
                        <option value="decrease_percent">Decrease by %</option>
                        <option value="set_fixed">Set Fixed Price</option>
                    </select>
                    <input type="number" id="bulk_price_value" step="0.01" placeholder="Enter value" style="margin-top: 0.5rem;">
                </div>

                <div class="form-group">
                    <label for="bulk_stock_type">Stock Adjustment</label>
                    <select id="bulk_stock_type">
                        <option value="">No Change</option>
                        <option value="increase">Increase by</option>
                        <option value="decrease">Decrease by</option>
                        <option value="set_fixed">Set to</option>
                    </select>
                    <input type="number" id="bulk_stock_value" placeholder="Enter value" style="margin-top: 0.5rem;">
                </div>

                <div class="form-group">
                    <label for="bulk_status">Status</label>
                    <select id="bulk_status">
                        <option value="">No Change</option>
                        <option value="1">Activate</option>
                        <option value="0">Deactivate</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="bulk_featured">Featured</label>
                    <select id="bulk_featured">
                        <option value="">No Change</option>
                        <option value="1">Mark as Featured</option>
                        <option value="0">Remove Featured</option>
                    </select>
                </div>

                <div style="margin-top: 2rem; display: flex; gap: 1rem; justify-content: flex-end;">
                    <button type="button" class="btn btn-outline" onclick="closeBulkEditModal()">Cancel</button>
                    <button type="submit" class="btn btn-success">Apply Changes</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const API_URL = 'http://localhost:8000/api/admin';
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            calculateStats();
            setupDragAndDrop();
            setupSearch();
        });

        // Calculate stats
        function calculateStats() {
            const products = @json($products['data'] ?? []);
            let activeCount = 0;
            let outOfStockCount = 0;
            let totalValue = 0;

            products.forEach(product => {
                if (product.is_active) activeCount++;
                if (product.stock === 0) outOfStockCount++;
                totalValue += (product.price || 0) * (product.stock || 0);
            });

            document.getElementById('activeCount').textContent = activeCount;
            document.getElementById('outOfStockCount').textContent = outOfStockCount;
            document.getElementById('totalValue').textContent = '$' + totalValue.toFixed(2);
        }

        // Search with debounce
        let searchTimeout;
        function setupSearch() {
            document.getElementById('searchInput').addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    applyFilters();
                }, 500);
            });
        }

        // Modal functions
        function openAddProductModal() {
            document.getElementById('modalTitle').textContent = 'Add New Product';
            document.getElementById('productForm').reset();
            document.getElementById('productId').value = '';
            document.getElementById('productModal').classList.add('active');
        }

        function closeProductModal() {
            document.getElementById('productModal').classList.remove('active');
        }

        function openBulkImportModal() {
            document.getElementById('bulkImportModal').classList.add('active');
        }

        function closeBulkImportModal() {
            document.getElementById('bulkImportModal').classList.remove('active');
        }

        function openBulkEditModal() {
            const selectedCount = document.querySelectorAll('.product-checkbox:checked').length;
            if (selectedCount === 0) {
                alert('Please select products first');
                return;
            }
            document.getElementById('bulkEditCount').textContent = selectedCount;
            document.getElementById('bulkEditModal').classList.add('active');
        }

        function closeBulkEditModal() {
            document.getElementById('bulkEditModal').classList.remove('active');
        }

        // Edit product
        function editProduct(product) {
            document.getElementById('modalTitle').textContent = 'Edit Product';
            document.getElementById('productId').value = product.id;
            document.getElementById('name').value = product.name || '';
            document.getElementById('sku').value = product.sku || '';
            document.getElementById('shortcode').value = product.shortcode || '';
            document.getElementById('price').value = product.price || '';
            document.getElementById('sale_price').value = product.sale_price || '';
            document.getElementById('cost_price').value = product.cost_price || '';
            document.getElementById('stock').value = product.stock || 0;
            document.getElementById('product_type').value = product.product_type || 'one_time';
            document.getElementById('category_id').value = product.category_id || '';
            document.getElementById('image_url').value = product.image_url || '';
            document.getElementById('short_description').value = product.short_description || '';
            document.getElementById('description').value = product.description || '';
            document.getElementById('specifications').value = product.specifications || '';
            document.getElementById('is_featured').checked = product.is_featured || false;
            document.getElementById('is_active').checked = product.is_active !== false;
            document.getElementById('productModal').classList.add('active');
        }

        // Save product
        async function saveProduct(event) {
            event.preventDefault();
            const form = event.target;
            const saveBtn = document.getElementById('saveBtn');
            const formData = new FormData(form);
            const productId = formData.get('id');
            const data = {};

            formData.forEach((value, key) => {
                if (key !== 'id') {
                    data[key] = value;
                }
            });

            data.is_featured = form.querySelector('#is_featured').checked ? 1 : 0;
            data.is_active = form.querySelector('#is_active').checked ? 1 : 0;

            const url = productId ? `${API_URL}/products/${productId}` : `${API_URL}/products`;
            const method = productId ? 'PUT' : 'POST';

            saveBtn.querySelector('.loading').style.display = 'inline-block';
            saveBtn.querySelector('.btn-text').textContent = 'Saving...';
            saveBtn.disabled = true;

            try {
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success) {
                    alert(productId ? 'Product updated successfully!' : 'Product created successfully!');
                    closeProductModal();
                    location.reload();
                } else {
                    alert('Error: ' + (result.message || 'Failed to save product'));
                }
            } catch (error) {
                alert('Error: ' + error.message);
            } finally {
                saveBtn.querySelector('.loading').style.display = 'none';
                saveBtn.querySelector('.btn-text').textContent = 'Save Product';
                saveBtn.disabled = false;
            }
        }

        // Delete product
        async function deleteProduct(id) {
            if (!confirm('Are you sure you want to delete this product?')) return;

            try {
                const response = await fetch(`${API_URL}/products/${id}`, {
                    method: 'DELETE',
                    headers: { 'Accept': 'application/json' }
                });

                const result = await response.json();

                if (result.success) {
                    alert('Product deleted successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + (result.message || 'Failed to delete product'));
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        }

        // Bulk actions
        function toggleSelectAll() {
            const selectAll = document.getElementById('selectAll').checked || document.getElementById('selectAllHeader').checked;
            document.querySelectorAll('.product-checkbox').forEach(checkbox => {
                checkbox.checked = selectAll;
            });
            document.getElementById('selectAll').checked = selectAll;
            document.getElementById('selectAllHeader').checked = selectAll;
            updateBulkActions();
        }

        function updateBulkActions() {
            const checkedBoxes = document.querySelectorAll('.product-checkbox:checked');
            const bulkActions = document.getElementById('bulkActions');
            const selectedCount = document.getElementById('selectedCount');

            if (checkedBoxes.length > 0) {
                bulkActions.classList.remove('hidden');
                selectedCount.textContent = `${checkedBoxes.length} selected`;
            } else {
                bulkActions.classList.add('hidden');
            }
        }

        async function bulkActivate() {
            const ids = Array.from(document.querySelectorAll('.product-checkbox:checked')).map(cb => cb.value);
            if (ids.length === 0) return;

            try {
                const response = await fetch(`${API_URL}/products/bulk-update-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ ids, is_active: true })
                });

                const result = await response.json();
                if (result.success) {
                    alert(`${result.updated_count} products activated!`);
                    location.reload();
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        }

        async function bulkDeactivate() {
            const ids = Array.from(document.querySelectorAll('.product-checkbox:checked')).map(cb => cb.value);
            if (ids.length === 0) return;

            try {
                const response = await fetch(`${API_URL}/products/bulk-update-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ ids, is_active: false })
                });

                const result = await response.json();
                if (result.success) {
                    alert(`${result.updated_count} products deactivated!`);
                    location.reload();
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        }

        async function bulkDelete() {
            const ids = Array.from(document.querySelectorAll('.product-checkbox:checked')).map(cb => cb.value);
            if (ids.length === 0) return;

            if (!confirm(`Are you sure you want to delete ${ids.length} products?`)) return;

            try {
                const response = await fetch(`${API_URL}/products/bulk-delete`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ ids })
                });

                const result = await response.json();
                if (result.success) {
                    alert(`${result.deleted_count} products deleted!`);
                    location.reload();
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        }

        async function saveBulkEdit(event) {
            event.preventDefault();
            const ids = Array.from(document.querySelectorAll('.product-checkbox:checked')).map(cb => cb.value);
            if (ids.length === 0) return;

            const data = { ids };

            const priceType = document.getElementById('bulk_price_type').value;
            const priceValue = document.getElementById('bulk_price_value').value;
            if (priceType && priceValue) {
                data.price_adjustment_type = priceType;
                data.price_adjustment_value = parseFloat(priceValue);
            }

            const stockType = document.getElementById('bulk_stock_type').value;
            const stockValue = document.getElementById('bulk_stock_value').value;
            if (stockType && stockValue) {
                data.stock_adjustment_type = stockType;
                data.stock_adjustment_value = parseInt(stockValue);
            }

            const status = document.getElementById('bulk_status').value;
            if (status !== '') {
                data.is_active = parseInt(status) === 1;
            }

            const featured = document.getElementById('bulk_featured').value;
            if (featured !== '') {
                data.is_featured = parseInt(featured) === 1;
            }

            try {
                const response = await fetch(`${API_URL}/products/bulk-edit`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();
                if (result.success) {
                    alert(result.message);
                    closeBulkEditModal();
                    location.reload();
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        }

        // Import functions
        function showImportTab(tab) {
            document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(tc => tc.classList.remove('active'));

            if (tab === 'csv') {
                document.querySelectorAll('.tab')[0].classList.add('active');
                document.getElementById('csvImport').classList.add('active');
            } else {
                document.querySelectorAll('.tab')[1].classList.add('active');
                document.getElementById('googleImport').classList.add('active');
            }
        }

        function setupDragAndDrop() {
            const uploadArea = document.getElementById('uploadArea');
            if (!uploadArea) return;

            uploadArea.addEventListener('dragover', (e) => {
                e.preventDefault();
                uploadArea.classList.add('dragover');
            });

            uploadArea.addEventListener('dragleave', () => {
                uploadArea.classList.remove('dragover');
            });

            uploadArea.addEventListener('drop', (e) => {
                e.preventDefault();
                uploadArea.classList.remove('dragover');
                const files = e.dataTransfer.files;
                if (files.length) {
                    document.getElementById('fileInput').files = files;
                    handleFileSelect({ target: { files } });
                }
            });
        }

        function handleFileSelect(event) {
            const file = event.target.files[0];
            if (file) {
                document.getElementById('fileName').textContent = file.name;
                document.getElementById('fileInfo').style.display = 'block';
            }
        }

        async function uploadFile() {
            const fileInput = document.getElementById('fileInput');
            const file = fileInput.files[0];
            if (!file) return;

            const formData = new FormData();
            formData.append('file', file);

            try {
                const response = await fetch(`${API_URL}/products/import-file`, {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                if (result.success) {
                    alert(`Import completed!\nImported: ${result.data.imported}\nUpdated: ${result.data.updated}\nFailed: ${result.data.failed}`);
                    closeBulkImportModal();
                    location.reload();
                } else {
                    alert('Import failed: ' + result.message);
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        }

        async function importFromGoogleSheets() {
            const url = document.getElementById('googleSheetUrl').value;
            const sheetName = document.getElementById('sheetName').value;

            if (!url) {
                alert('Please enter a Google Sheets URL');
                return;
            }

            try {
                const response = await fetch(`${API_URL}/products/import-google-sheets`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ url, sheet_name: sheetName })
                });

                const result = await response.json();
                alert(result.message);
                if (result.success) {
                    closeBulkImportModal();
                    location.reload();
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        }

        // Export products
        function exportProducts() {
            window.location.href = `${API_URL}/products/export`;
        }

        // Filter functions
        function applyFilters() {
            const search = document.getElementById('searchInput').value;
            const category = document.getElementById('categoryFilter').value;
            const status = document.getElementById('statusFilter').value;
            const params = new URLSearchParams();
            if (search) params.append('search', search);
            if (category) params.append('category', category);
            if (status) params.append('status', status);
            window.location.href = `?${params.toString()}`;
        }

        function clearFilters() {
            window.location.href = window.location.pathname;
        }
    </script>
</body>
</html>
