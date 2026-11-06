<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\Category;
use App\Models\Vendor;

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

    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $vendors = Vendor::where('status', 'active')->orderBy('shop_name')->get();

        return view('admin.products.create', compact('categories', 'vendors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'vendor_id' => 'nullable|exists:vendors,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0.01',
            'sale_price' => 'nullable|numeric|min:0.01|lt:price',
            'stock_quantity' => 'required|integer|min:0',
            'sku' => 'required|string|max:50|unique:products,sku',
            'short_description' => 'nullable|string|max:500',
            'description' => 'required|string',
            'image_files' => 'nullable|array',
            'image_files.*' => 'nullable|image|mimes:jpeg,png,jpg,webp,avif,gif|max:10240',
            'images' => 'nullable|string',
            'spec_keys' => 'nullable|array',
            'spec_values' => 'nullable|array',
            'status' => 'required|in:approved,pending,rejected',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
        ]);

        // Process device uploaded image files
        $images = [];
        if ($request->hasFile('image_files')) {
            foreach ($request->file('image_files') as $file) {
                if ($file && $file->isValid()) {
                    $path = $file->store('products', 'public');
                    $images[] = $path;
                }
            }
        }

        // Process fallback / supplemental URLs
        if (!empty($validated['images'])) {
            $urlImages = array_map('trim', explode("\n", str_replace(["\r", ","], "\n", $validated['images'])));
            $urlImages = array_values(array_filter($urlImages));
            $images = array_merge($images, $urlImages);
        }

        if (empty($images)) {
            $images = ['https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=800&auto=format&fit=crop&q=80'];
        }

        // Process specifications
        $specs = [];
        if (!empty($validated['spec_keys']) && !empty($validated['spec_values'])) {
            foreach ($validated['spec_keys'] as $idx => $key) {
                if (!empty(trim($key)) && isset($validated['spec_values'][$idx])) {
                    $specs[trim($key)] = trim($validated['spec_values'][$idx]);
                }
            }
        }

        $slug = Str::slug($validated['name']);
        if (Product::where('slug', $slug)->exists()) {
            $slug .= '-' . rand(100, 9999);
        }

        $vendorId = $validated['vendor_id'] ?? Vendor::first()?->id ?? 1;

        Product::create([
            'vendor_id' => $vendorId,
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'slug' => $slug,
            'price' => $validated['price'],
            'sale_price' => $validated['sale_price'] ?? null,
            'stock_quantity' => $validated['stock_quantity'],
            'sku' => strtoupper($validated['sku']),
            'short_description' => $validated['short_description'] ?? null,
            'description' => $validated['description'],
            'images' => $images,
            'specifications' => $specs,
            'is_active' => $request->boolean('is_active', true),
            'is_featured' => $request->boolean('is_featured', false),
            'status' => $validated['status'],
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Product created and added to catalog.');
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $vendors = Vendor::where('status', 'active')->orderBy('shop_name')->get();

        return view('admin.products.edit', compact('product', 'categories', 'vendors'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'vendor_id' => 'nullable|exists:vendors,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0.01',
            'sale_price' => 'nullable|numeric|min:0.01',
            'stock_quantity' => 'required|integer|min:0',
            'sku' => "required|string|max:50|unique:products,sku,{$product->id}",
            'short_description' => 'nullable|string|max:500',
            'description' => 'required|string',
            'image_files' => 'nullable|array',
            'image_files.*' => 'nullable|image|mimes:jpeg,png,jpg,webp,avif,gif|max:10240',
            'existing_images' => 'nullable|array',
            'images' => 'nullable|string',
            'spec_keys' => 'nullable|array',
            'spec_values' => 'nullable|array',
            'status' => 'required|in:approved,pending,rejected',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
        ]);

        // Process retained existing images
        $images = [];
        if ($request->has('existing_images')) {
            $images = array_values(array_filter($request->input('existing_images', [])));
        } else {
            $images = is_array($product->images) ? $product->images : [];
        }

        // Process new uploaded files from user's device
        if ($request->hasFile('image_files')) {
            foreach ($request->file('image_files') as $file) {
                if ($file && $file->isValid()) {
                    $path = $file->store('products', 'public');
                    $images[] = $path;
                }
            }
        }

        // Process any supplemental URLs entered
        if (!empty($validated['images'])) {
            $urlImages = array_map('trim', explode("\n", str_replace(["\r", ","], "\n", $validated['images'])));
            $urlImages = array_values(array_filter($urlImages));
            $images = array_merge($images, $urlImages);
        }

        // Clean up removed local files from storage
        if (is_array($product->images)) {
            $removed = array_diff($product->images, $images);
            foreach ($removed as $oldImg) {
                if (!empty($oldImg) && !str_starts_with($oldImg, 'http') && Storage::disk('public')->exists($oldImg)) {
                    Storage::disk('public')->delete($oldImg);
                }
            }
        }

        if (empty($images)) {
            $images = ['https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=800&auto=format&fit=crop&q=80'];
        }

        // Process specifications
        $specs = [];
        if (!empty($validated['spec_keys']) && !empty($validated['spec_values'])) {
            foreach ($validated['spec_keys'] as $idx => $key) {
                if (!empty(trim($key)) && isset($validated['spec_values'][$idx])) {
                    $specs[trim($key)] = trim($validated['spec_values'][$idx]);
                }
            }
        } else {
            $specs = $product->specifications;
        }

        $product->update([
            'category_id' => $validated['category_id'],
            'vendor_id' => $validated['vendor_id'] ?? $product->vendor_id,
            'name' => $validated['name'],
            'price' => $validated['price'],
            'sale_price' => $validated['sale_price'] ?? null,
            'stock_quantity' => $validated['stock_quantity'],
            'sku' => strtoupper($validated['sku']),
            'short_description' => $validated['short_description'] ?? null,
            'description' => $validated['description'],
            'images' => $images,
            'specifications' => $specs,
            'status' => $validated['status'],
            'is_active' => $request->boolean('is_active', true),
            'is_featured' => $request->boolean('is_featured', false),
        ]);

        return redirect()->route('admin.products.index')->with('success', "Product '{$product->name}' updated successfully.");
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

        // Delete any local storage images
        if (is_array($product->images)) {
            foreach ($product->images as $img) {
                if (!empty($img) && !str_starts_with($img, 'http') && Storage::disk('public')->exists($img)) {
                    Storage::disk('public')->delete($img);
                }
            }
        }

        $product->delete();

        return back()->with('info', 'Product removed from system catalog.');
    }
}
