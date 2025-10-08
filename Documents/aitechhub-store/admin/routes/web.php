<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    // Analytics Dashboard
    Route::get('/analytics', [\App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('analytics');

    // Product Management
    Route::get('/products', function () { return redirect('/admin/products'); })->name('products');
    Route::get('/admin/products', [\App\Http\Controllers\Admin\ProductController::class, 'index'])->name('admin.products');
    Route::get('/admin/products/enhanced', [\App\Http\Controllers\Admin\ProductController::class, 'enhanced'])->name('admin.products.enhanced');
    Route::post('/admin/products/seed', [\App\Http\Controllers\Admin\SeedController::class, 'productsSeed'])->name('admin.products.seed');
    Route::post('/admin/products/clear', [\App\Http\Controllers\Admin\SeedController::class, 'productsClear'])->name('admin.products.clear');

    // Reports
    Route::get('/reports', [\App\Http\Controllers\Admin\ReportsController::class, 'index'])->name('reports');
    Route::get('/reports/sales', [\App\Http\Controllers\Admin\ReportsController::class, 'sales'])->name('reports.sales');
    Route::get('/reports/products', [\App\Http\Controllers\Admin\ReportsController::class, 'products'])->name('reports.products');
    Route::get('/reports/categories', [\App\Http\Controllers\Admin\ReportsController::class, 'categories'])->name('reports.categories');
    Route::get('/reports/inventory', [\App\Http\Controllers\Admin\ReportsController::class, 'inventory'])->name('reports.inventory');
    Route::get('/reports/financial', [\App\Http\Controllers\Admin\ReportsController::class, 'financial'])->name('reports.financial');

    // Order Management
    Route::get('/orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders');
    Route::get('/orders/{id}', [\App\Http\Controllers\Admin\OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{id}/status', [\App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::post('/orders/{id}/tracking', [\App\Http\Controllers\Admin\OrderController::class, 'addTracking'])->name('orders.add-tracking');
    Route::post('/orders/{id}/refund', [\App\Http\Controllers\Admin\OrderController::class, 'refund'])->name('orders.refund');
    Route::post('/admin/orders/seed', [\App\Http\Controllers\Admin\SeedController::class, 'ordersSeed'])->name('admin.orders.seed');
    Route::post('/admin/orders/clear', [\App\Http\Controllers\Admin\SeedController::class, 'ordersClear'])->name('admin.orders.clear');

    // Customer Management
    Route::get('/customers', [\App\Http\Controllers\Admin\CustomerController::class, 'index'])->name('customers');
    Route::get('/customers/{id}', [\App\Http\Controllers\Admin\CustomerController::class, 'show'])->name('customers.show');

    // Settings & Payments (placeholders)
    Route::view('/payments', 'pages.payments')->name('payments');
    Route::view('/settings', 'pages.settings')->name('settings');

    // Deployment Information
    Route::get('/deployment', [\App\Http\Controllers\Admin\DeploymentController::class, 'index'])->name('deployment');
});

require __DIR__.'/auth.php';
