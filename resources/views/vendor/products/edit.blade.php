@extends('layouts.vendor')

@section('page-title', 'Edit ' . $product->name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-black text-white tracking-tight">Edit Product Specifications</h2>
            <p class="text-xs text-slate-500 mt-0.5">Update pricing, inventory, descriptions, and media assets</p>
        </div>
        <a href="{{ route('vendor.products.index') }}" class="text-xs text-slate-400 hover:text-white">
            &larr; Back to Catalog
        </a>
    </div>

    <form action="{{ route('vendor.products.update', $product->id) }}" method="POST" class="space-y-6 text-xs">
        @csrf
        @method('PUT')

        <!-- Core Details -->
        <div class="bg-slate-950 p-6 sm:p-8 rounded-3xl border border-slate-800 shadow-xl space-y-4">
            <h3 class="font-bold text-white uppercase tracking-wider text-xs pb-3 border-b border-slate-850">
                Core Metadata
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block font-bold text-slate-300 mb-1">Product Title</label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                           class="w-full rounded-xl border border-slate-800 bg-slate-900 px-3.5 py-2.5 text-white">
                </div>

                <div>
                    <label class="block font-bold text-slate-300 mb-1">Category</label>
                    <select name="category_id" required class="w-full rounded-xl border border-slate-800 bg-slate-900 px-3.5 py-2.5 text-white">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ $product->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-300 mb-1">SKU</label>
                    <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" required
                           class="w-full font-mono rounded-xl border border-slate-800 bg-slate-900 px-3.5 py-2.5 text-white uppercase">
                </div>

                <div>
                    <label class="block font-bold text-slate-300 mb-1">Retail Price ($)</label>
                    <input type="number" step="0.01" name="price" value="{{ old('price', $product->price) }}" required
                           class="w-full rounded-xl border border-slate-800 bg-slate-900 px-3.5 py-2.5 text-white">
                </div>

                <div>
                    <label class="block font-bold text-slate-300 mb-1">Sale Price ($)</label>
                    <input type="number" step="0.01" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}"
                           class="w-full rounded-xl border border-slate-800 bg-slate-900 px-3.5 py-2.5 text-white">
                </div>

                <div>
                    <label class="block font-bold text-slate-300 mb-1">Stock Quantity</label>
                    <input type="number" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" required min="0"
                           class="w-full rounded-xl border border-slate-800 bg-slate-900 px-3.5 py-2.5 text-white">
                </div>

                <div class="flex items-center gap-4 pt-5">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ $product->is_active ? 'checked' : '' }} class="rounded border-slate-700 bg-slate-900 text-indigo-600">
                        <span class="text-white font-medium">Active (Visible in Catalog)</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_featured" value="1" {{ $product->is_featured ? 'checked' : '' }} class="rounded border-slate-700 bg-slate-900 text-indigo-600">
                        <span class="text-white font-medium">Featured on Homepage</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Descriptions -->
        <div class="bg-slate-950 p-6 sm:p-8 rounded-3xl border border-slate-800 shadow-xl space-y-4">
            <h3 class="font-bold text-white uppercase tracking-wider text-xs pb-3 border-b border-slate-850">
                Descriptions
            </h3>

            <div>
                <label class="block font-bold text-slate-300 mb-1">Short Description</label>
                <input type="text" name="short_description" value="{{ old('short_description', $product->short_description) }}"
                       class="w-full rounded-xl border border-slate-800 bg-slate-900 px-3.5 py-2.5 text-white">
            </div>

            <div>
                <label class="block font-bold text-slate-300 mb-1">Full Description</label>
                <textarea name="description" rows="5" required class="w-full rounded-xl border border-slate-800 bg-slate-900 px-3.5 py-2.5 text-white">{{ old('description', $product->description) }}</textarea>
            </div>
        </div>

        <!-- Image URLs -->
        <div class="bg-slate-950 p-6 sm:p-8 rounded-3xl border border-slate-800 shadow-xl space-y-4">
            <h3 class="font-bold text-white uppercase tracking-wider text-xs pb-3 border-b border-slate-850">
                Image URLs (one per line)
            </h3>
            <textarea name="images" rows="3" class="w-full font-mono text-[11px] rounded-xl border border-slate-800 bg-slate-900 px-3.5 py-2.5 text-slate-300">{{ implode("\n", $product->gallery_images) }}</textarea>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end gap-3 pt-4">
            <a href="{{ route('vendor.products.index') }}" class="px-5 py-2.5 text-slate-400 hover:text-white font-medium">Cancel</a>
            <button type="submit" class="py-3 px-8 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold uppercase tracking-wider shadow-xl shadow-indigo-600/30 transition-all active:scale-95">
                Save Changes
            </button>
        </div>
    </form>
</div>
@endsection
