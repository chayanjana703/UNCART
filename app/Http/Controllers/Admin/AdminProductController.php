<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class AdminProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['vendor', 'category']);

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($catId = $request->input('category_id')) {
            $query->where('category_id', $catId);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $products = $query->latest()->paginate(15)->withQueryString();
        $categories = Category::all();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function updateStatus(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $status = $request->input('status', 'approved');

        $product->status = $status;
        if ($status === 'approved') {
            $product->is_active = true;
        }
        $product->save();

        return back()->with('success', "Product status changed to {$status}.");
    }

    public function toggleFeatured($id)
    {
        $product = Product::findOrFail($id);
        $product->is_featured = !$product->is_featured;
        $product->save();

        $state = $product->is_featured ? 'Marked as Featured' : 'Unmarked from Featured';
        return back()->with('success', "Product {$state}.");
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return back()->with('info', 'Product removed from system catalog.');
    }
}
