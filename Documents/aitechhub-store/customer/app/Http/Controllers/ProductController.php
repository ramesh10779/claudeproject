<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::query();

        if ($search = $request->string('q')->toString()) {
            $query->where('name', 'like', "%{$search}%");
        }
        if ($type = $request->string('type')->toString()) {
            if (in_array($type, ['one_time', 'subscription'])) {
                $query->where('product_type', $type);
            }
        }
        if ($categorySlug = $request->string('category')->toString()) {
            $query->whereHas('category', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }

        $products = $query->latest()->paginate(24)->withQueryString();
        $categories = Category::orderBy('name')->get();
        return view('pages.products', compact('products', 'categories'));
    }

    public function show(Product $product): View
    {
        return view('pages.product', compact('product'));
    }

    public function search(Request $request): JsonResponse
    {
        $query = $request->string('q')->toString();

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $products = Product::where('name', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->orWhere('sku', 'like', "%{$query}%")
            ->where('is_active', true)
            ->limit(10)
            ->get(['id', 'name', 'price', 'image_url', 'sku']);

        return response()->json($products->map(function($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => number_format($product->price, 2),
                'image' => $product->image_url ?? 'https://via.placeholder.com/50/2a5298/ffffff?text=No+Image',
                'url' => route('product.show', $product->id)
            ];
        }));
    }
}
