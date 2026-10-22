<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Vendor;
use App\Models\Product;
use App\Models\Category;

class VendorProductController extends Controller
{
    private function getVendor(): Vendor
    {
        return Vendor::firstOrCreate(
            ['user_id' => Auth::id()],
            [
                'shop_name' => Auth::user()->name . "'s Shop",
                'shop_slug' => Str::slug(Auth::user()->name . '-' . rand(100, 999)),
                'status' => 'active',
                'kyc_status' => 'verified',
            ]
        );
    }

    public function index(Request $request)
    {
        $vendor = $this->getVendor();

        $query = Product::where('vendor_id', $vendor->id)->with('category');

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $products = $query->latest()->paginate(10)->withQueryString();

        return view('vendor.products.index', compact('products', 'vendor'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        return view('vendor.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $vendor = $this->getVendor();

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0.01',
            'sale_price' => 'nullable|numeric|min:0.01|lt:price',
            'stock_quantity' => 'required|integer|min:0',
            'sku' => 'required|string|max:50|unique:products,sku',
            'short_description' => 'nullable|string|max:500',
            'description' => 'required|string',
            'images' => 'nullable|string', // Comma separated URLs or JSON
            'spec_keys' => 'nullable|array',
            'spec_values' => 'nullable|array',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
        ]);

        // Process images
        $images = [];
        if (!empty($validated['images'])) {
            $images = array_map('trim', explode("\n", str_replace(["\r", ","], "\n", $validated['images'])));
            $images = array_values(array_filter($images));
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

        Product::create([
            'vendor_id' => $vendor->id,
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
            'status' => 'approved',
        ]);

        return redirect()->route('vendor.products.index')->with('success', 'Product published successfully.');
    }

    public function edit($id)
    {
        $vendor = $this->getVendor();
        $product = Product::where('id', $id)->where('vendor_id', $vendor->id)->firstOrFail();
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('vendor.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $vendor = $this->getVendor();
        $product = Product::where('id', $id)->where('vendor_id', $vendor->id)->firstOrFail();

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0.01',
            'sale_price' => 'nullable|numeric|min:0.01',
            'stock_quantity' => 'required|integer|min:0',
            'sku' => "required|string|max:50|unique:products,sku,{$product->id}",
            'short_description' => 'nullable|string|max:500',
            'description' => 'required|string',
            'images' => 'nullable|string',
            'spec_keys' => 'nullable|array',
            'spec_values' => 'nullable|array',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
        ]);

        $images = [];
        if (!empty($validated['images'])) {
            $images = array_map('trim', explode("\n", str_replace(["\r", ","], "\n", $validated['images'])));
            $images = array_values(array_filter($images));
        } else {
            $images = $product->images;
        }

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
            'name' => $validated['name'],
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
        ]);

        return redirect()->route('vendor.products.index')->with('success', "Product '{$product->name}' updated.");
    }

    public function destroy($id)
    {
        $vendor = $this->getVendor();
        $product = Product::where('id', $id)->where('vendor_id', $vendor->id)->firstOrFail();
        $product->delete();

        return redirect()->route('vendor.products.index')->with('info', 'Product removed from catalog.');
    }

    public function toggleStatus($id)
    {
        $vendor = $this->getVendor();
        $product = Product::where('id', $id)->where('vendor_id', $vendor->id)->firstOrFail();
        $product->is_active = !$product->is_active;
        $product->save();

        $state = $product->is_active ? 'Activated' : 'Deactivated';
        return back()->with('success', "Product {$state}.");
    }

    public function bulkUpload(Request $request)
    {
        $vendor = $this->getVendor();
        $defaultCat = Category::first();

        // Sample bulk insertion simulation
        $count = 0;
        if ($request->hasFile('csv_file')) {
            $file = $request->file('csv_file');
            $handle = fopen($file->getRealPath(), 'r');
            $header = fgetcsv($handle); // skip header

            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) >= 4) {
                    $name = trim($row[0]);
                    $price = (float) trim($row[1]);
                    $stock = (int) trim($row[2]);
                    $sku = trim($row[3]);

                    if ($name && $price > 0 && !Product::where('sku', $sku)->exists()) {
                        Product::create([
                            'vendor_id' => $vendor->id,
                            'category_id' => $defaultCat ? $defaultCat->id : 1,
                            'name' => $name,
                            'slug' => Str::slug($name . '-' . rand(100, 999)),
                            'price' => $price,
                            'stock_quantity' => $stock,
                            'sku' => $sku,
                            'short_description' => 'Imported via CSV bulk upload catalog manager.',
                            'description' => 'Full technical details and descriptions can be customized by the vendor.',
                            'images' => ['https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=800&auto=format&fit=crop&q=80'],
                            'is_active' => true,
                            'status' => 'approved',
                        ]);
                        $count++;
                    }
                }
            }
            fclose($handle);
        } else {
            // Quick mock bulk upload if no file is chosen
            $sampleNames = [
                ['name' => 'Tactile Aluminum Wrist Rest', 'price' => 45.00, 'stock' => 20, 'sku' => 'BULK-WR-' . rand(100, 999)],
                ['name' => 'Braided USB-C Aviator Cable', 'price' => 28.00, 'stock' => 50, 'sku' => 'BULK-CB-' . rand(100, 999)],
            ];
            foreach ($sampleNames as $s) {
                Product::create([
                    'vendor_id' => $vendor->id,
                    'category_id' => $defaultCat ? $defaultCat->id : 1,
                    'name' => $s['name'],
                    'slug' => Str::slug($s['name'] . '-' . rand(100, 999)),
                    'price' => $s['price'],
                    'stock_quantity' => $s['stock'],
                    'sku' => $s['sku'],
                    'short_description' => 'Bulk imported inventory batch item.',
                    'description' => 'High-grade accessories manufactured to high tolerances.',
                    'images' => ['https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=800&auto=format&fit=crop&q=80'],
                    'is_active' => true,
                    'status' => 'approved',
                ]);
                $count++;
            }
        }

        return back()->with('success', "Bulk inventory processed: {$count} products imported successfully.");
    }

    public function downloadTemplate()
    {
        $csv = "Product Name,Price,Stock Quantity,SKU\n";
        $csv .= "Example Matte Keycap Set,65.00,25,APX-KC-01\n";
        $csv .= "Desk Leather Mat XL,42.00,40,LX-MAT-02\n";

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="shopicart_product_bulk_template.csv"',
        ]);
    }
}
