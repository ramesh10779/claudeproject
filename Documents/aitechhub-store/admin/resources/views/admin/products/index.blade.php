<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Products</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif; background: #f7fafc; }
        .container { max-width: 1400px; margin: 0 auto; padding: 2rem; }
        h1 { font-size: 2rem; color: #1f2937; }
        .alert { margin: 1rem 0; padding: 1rem; background: #ecfeff; border: 1px solid #a5f3fc; border-radius: 8px; color: #155e75; }
        .actions { display: flex; gap: 1rem; margin-bottom: 2rem; flex-wrap: wrap; }
        .btn { display: inline-block; padding: 0.75rem 1.5rem; border-radius: 8px; background: #667eea; color: #fff; text-decoration: none; border: none; cursor: pointer; font-weight: 500; transition: all 0.2s; font-size: 0.95rem; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4); }
        .btn-success { background: #10b981; }
        .btn-success:hover { box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4); }
        .btn-outline { background: #fff; color: #667eea; border: 2px solid #667eea; }
        .btn-outline:hover { background: #667eea; color: white; }
        .btn-small { padding: 0.5rem 1rem; font-size: 0.85rem; }
        .table-wrapper { background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; }
        thead { background: #f9fafb; }
        th { padding: 1rem; border-bottom: 2px solid #e5e7eb; text-align: left; font-weight: 600; color: #374151; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px; }
        td { padding: 1rem; border-bottom: 1px solid #e5e7eb; color: #6b7280; }
        tbody tr:hover { background: #f9fafb; }
        .badge { display: inline-block; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.85rem; font-weight: 500; }
        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .pagination { margin-top: 1.5rem; padding: 1rem; background: #f9fafb; border-radius: 8px; display: flex; justify-content: space-between; align-items: center; }

        /* Modal Styles */
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; }
        .modal.active { display: flex; }
        .modal-content { background: white; border-radius: 16px; padding: 2rem; max-width: 600px; width: 90%; max-height: 90vh; overflow-y: auto; }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
        .modal-header h2 { font-size: 1.5rem; color: #1f2937; }
        .close-btn { background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #9ca3af; }
        .close-btn:hover { color: #374151; }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 500; color: #374151; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 0.95rem; }
        .form-group textarea { min-height: 100px; resize: vertical; }
        .form-actions { display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem; }
    </style>
</head>
<body>
    @include('components.navbar', ['title' => '📦 Admin Products', 'backUrl' => '/dashboard'])

    <div class="container">

        @if(session('status'))
            <div class="alert">{{ session('status') }}</div>
        @endif

        <!-- Action Buttons -->
        <div class="actions">
            <button onclick="openAddProductModal()" class="btn">➕ Add Product</button>
            <a href="{{ route('admin.products.enhanced') }}" class="btn btn-success">📊 Enhanced Upload & Bulk Management</a>
            <a href="{{ route('deployment') }}" class="btn" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">🚀 Deployment Info</a>
            <a href="/products" class="btn btn-outline">View Store Products</a>
        </div>

        <!-- Products Table -->
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>SKU</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products['data'] ?? [] as $p)
                        <tr>
                            <td><strong>{{ $p['name'] ?? 'N/A' }}</strong></td>
                            <td>{{ $p['sku'] ?? 'N/A' }}</td>
                            <td>${{ number_format($p['price'] ?? 0, 2) }}</td>
                            <td>{{ $p['stock'] ?? 0 }}</td>
                            <td>
                                @if(($p['is_active'] ?? false))
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-danger">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <button onclick='editProduct(@json($p))' class="btn btn-small">✏️ Edit</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" style="text-align: center;">No products found. Click "Add Product" to create one.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        @if(isset($products['last_page']) && $products['last_page'] > 1)
            <div class="pagination">
                <span>Page {{ $products['current_page'] }} of {{ $products['last_page'] }}</span>
                <span>Total: {{ $products['total'] }} products</span>
            </div>
        @endif
    </div>

    <!-- Add/Edit Product Modal -->
    <div id="productModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Add Product</h2>
                <button class="close-btn" onclick="closeProductModal()">&times;</button>
            </div>
            <form id="productForm" onsubmit="saveProduct(event)">
                <input type="hidden" id="productId" name="id">

                <div class="form-group">
                    <label for="name">Product Name *</label>
                    <input type="text" id="name" name="name" required>
                </div>

                <div class="form-group">
                    <label for="sku">SKU *</label>
                    <input type="text" id="sku" name="sku" required>
                </div>

                <div class="form-group">
                    <label for="shortcode">Shortcode</label>
                    <input type="text" id="shortcode" name="shortcode" placeholder="Auto-generated if empty">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="price">Price *</label>
                        <input type="number" id="price" name="price" step="0.01" required>
                    </div>

                    <div class="form-group">
                        <label for="sale_price">Sale Price</label>
                        <input type="number" id="sale_price" name="sale_price" step="0.01" placeholder="Optional">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="stock">Stock *</label>
                        <input type="number" id="stock" name="stock" required>
                    </div>

                    <div class="form-group">
                        <label for="cost_price">Cost Price</label>
                        <input type="number" id="cost_price" name="cost_price" step="0.01" placeholder="Optional">
                    </div>
                </div>

                <div class="form-group">
                    <label for="product_type">Product Type *</label>
                    <select id="product_type" name="product_type" required>
                        <option value="one_time">One Time Purchase</option>
                        <option value="subscription">Subscription</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description"></textarea>
                </div>

                <div class="form-group">
                    <label for="image_url">Image URL</label>
                    <input type="url" id="image_url" name="image_url" placeholder="https://...">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="is_active">Status</label>
                        <select id="is_active" name="is_active">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="is_featured">Featured</label>
                        <select id="is_featured" name="is_featured">
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-outline" onclick="closeProductModal()">Cancel</button>
                    <button type="submit" class="btn">Save Product</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const API_BASE = 'http://localhost:8000/api/admin';

        function openAddProductModal() {
            document.getElementById('modalTitle').textContent = 'Add Product';
            document.getElementById('productForm').reset();
            document.getElementById('productId').value = '';
            document.getElementById('productModal').classList.add('active');
        }

        function editProduct(product) {
            document.getElementById('modalTitle').textContent = 'Edit Product';
            document.getElementById('productId').value = product.id || '';
            document.getElementById('name').value = product.name || '';
            document.getElementById('sku').value = product.sku || '';
            document.getElementById('shortcode').value = product.shortcode || '';
            document.getElementById('price').value = product.price || '';
            document.getElementById('sale_price').value = product.sale_price || '';
            document.getElementById('cost_price').value = product.cost_price || '';
            document.getElementById('stock').value = product.stock || '';
            document.getElementById('product_type').value = product.product_type || 'one_time';
            document.getElementById('description').value = product.description || '';
            document.getElementById('image_url').value = product.image_url || '';
            document.getElementById('is_active').value = product.is_active ? '1' : '0';
            document.getElementById('is_featured').value = product.is_featured ? '1' : '0';
            document.getElementById('productModal').classList.add('active');
        }

        function closeProductModal() {
            document.getElementById('productModal').classList.remove('active');
        }

        async function saveProduct(event) {
            event.preventDefault();

            const formData = new FormData(event.target);
            const productId = formData.get('id');
            const isEdit = productId !== '';

            const data = {
                name: formData.get('name'),
                sku: formData.get('sku'),
                shortcode: formData.get('shortcode') || undefined,
                price: parseFloat(formData.get('price')),
                sale_price: formData.get('sale_price') ? parseFloat(formData.get('sale_price')) : undefined,
                cost_price: formData.get('cost_price') ? parseFloat(formData.get('cost_price')) : undefined,
                stock: parseInt(formData.get('stock')),
                product_type: formData.get('product_type'),
                description: formData.get('description') || undefined,
                image_url: formData.get('image_url') || undefined,
                is_active: formData.get('is_active') === '1',
                is_featured: formData.get('is_featured') === '1'
            };

            // Remove undefined values
            Object.keys(data).forEach(key => data[key] === undefined && delete data[key]);

            try {
                const url = isEdit ? `${API_BASE}/products/${productId}` : `${API_BASE}/products`;
                const method = isEdit ? 'PUT' : 'POST';

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    alert(isEdit ? 'Product updated successfully!' : 'Product created successfully!');
                    closeProductModal();
                    window.location.reload();
                } else {
                    // Show detailed error messages
                    let errorMsg = 'Failed to save product';
                    if (result.errors) {
                        const errors = Object.values(result.errors).flat();
                        errorMsg = errors.join('\n');
                    } else if (result.message) {
                        errorMsg = result.message;
                    }
                    alert('Error: ' + errorMsg);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to save product. Please try again.');
            }
        }

        // Close modal when clicking outside
        document.getElementById('productModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeProductModal();
            }
        });
    </script>
</body>
</html>
