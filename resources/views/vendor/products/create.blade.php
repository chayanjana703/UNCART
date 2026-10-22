@extends('layouts.vendor')

@section('page-title', 'Create New Product')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{
    specs: [
        { key: 'Dimensions', value: '' },
        { key: 'Weight', value: '' },
        { key: 'Materials', value: '' },
        { key: 'Warranty', value: '1 Year Manufacturer' }
    ],
    addSpec() { this.specs.push({ key: '', value: '' }); },
    removeSpec(idx) { this.specs.splice(idx, 1); }
}">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-black text-white tracking-tight">Add New Product to Shop</h2>
            <p class="text-xs text-slate-500 mt-0.5">Specify hardware attributes, multi-angle imagery, and pricing structures</p>
        </div>
        <a href="{{ route('vendor.products.index') }}" class="text-xs text-slate-400 hover:text-white">
            &larr; Back to Catalog
        </a>
    </div>

    <form action="{{ route('vendor.products.store') }}" method="POST" class="space-y-6 text-xs">
        @csrf

        @if($errors->any())
            <div class="p-4 rounded-2xl bg-rose-950/60 border border-rose-800 text-rose-300">
                {{ $errors->first() }}
            </div>
        @endif

        <!-- Card 1: Core Details -->
        <div class="bg-slate-950 p-6 sm:p-8 rounded-3xl border border-slate-800 shadow-xl space-y-4">
            <h3 class="font-bold text-white uppercase tracking-wider text-xs pb-3 border-b border-slate-850">
                General Product Information
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block font-bold text-slate-300 mb-1">Product Title</label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Apex Titanium Mechanical Keyboard"
                           class="w-full rounded-xl border border-slate-800 bg-slate-900 px-3.5 py-2.5 text-white">
                </div>

                <div>
                    <label class="block font-bold text-slate-300 mb-1">Category</label>
                    <select name="category_id" required class="w-full rounded-xl border border-slate-800 bg-slate-900 px-3.5 py-2.5 text-white">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-300 mb-1">Stock Keeping Unit (SKU)</label>
                    <input type="text" name="sku" value="{{ old('sku', 'APX-' . strtoupper(\Illuminate\Support\Str::random(6))) }}" required
                           class="w-full font-mono rounded-xl border border-slate-800 bg-slate-900 px-3.5 py-2.5 text-white uppercase">
                </div>

                <div>
                    <label class="block font-bold text-slate-300 mb-1">Standard Retail Price ($)</label>
                    <input type="number" step="0.01" name="price" value="{{ old('price', '199.00') }}" required
                           class="w-full rounded-xl border border-slate-800 bg-slate-900 px-3.5 py-2.5 text-white">
                </div>

                <div>
                    <label class="block font-bold text-slate-300 mb-1">Discounted Sale Price (Optional)</label>
                    <input type="number" step="0.01" name="sale_price" value="{{ old('sale_price') }}" placeholder="Leave blank if not on sale"
                           class="w-full rounded-xl border border-slate-800 bg-slate-900 px-3.5 py-2.5 text-white">
                </div>

                <div>
                    <label class="block font-bold text-slate-300 mb-1">Inventory Quantity in Stock</label>
                    <input type="number" name="stock_quantity" value="{{ old('stock_quantity', '25') }}" required min="0"
                           class="w-full rounded-xl border border-slate-800 bg-slate-900 px-3.5 py-2.5 text-white">
                </div>

                <div class="flex items-center gap-4 pt-5">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" checked class="rounded border-slate-700 bg-slate-900 text-indigo-600">
                        <span class="text-white font-medium">Publish Active Immediately</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_featured" value="1" class="rounded border-slate-700 bg-slate-900 text-indigo-600">
                        <span class="text-white font-medium">Feature on Homepage</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Card 2: Descriptions -->
        <div class="bg-slate-950 p-6 sm:p-8 rounded-3xl border border-slate-800 shadow-xl space-y-4">
            <h3 class="font-bold text-white uppercase tracking-wider text-xs pb-3 border-b border-slate-850">
                Description & Copy
            </h3>

            <div>
                <label class="block font-bold text-slate-300 mb-1">Short Elevator Pitch (Appears on product cards)</label>
                <input type="text" name="short_description" value="{{ old('short_description') }}" placeholder="Brief summary of key value points..."
                       class="w-full rounded-xl border border-slate-800 bg-slate-900 px-3.5 py-2.5 text-white">
            </div>

            <div>
                <label class="block font-bold text-slate-300 mb-1">Full Detailed Specifications & Description</label>
                <textarea name="description" rows="5" required placeholder="Comprehensive materials overview, ergonomics, compatibility, packaging contents..."
                          class="w-full rounded-xl border border-slate-800 bg-slate-900 px-3.5 py-2.5 text-white">{{ old('description') }}</textarea>
            </div>
        </div>

        <!-- Card 3: Multi-Image URLs -->
        <div class="bg-slate-950 p-6 sm:p-8 rounded-3xl border border-slate-800 shadow-xl space-y-4">
            <h3 class="font-bold text-white uppercase tracking-wider text-xs pb-3 border-b border-slate-850">
                Gallery Images
            </h3>
            <p class="text-slate-500 text-[11px]">Enter image URLs (one per line). The first URL will be used as the primary hero thumbnail.</p>
            <textarea name="images" rows="3" placeholder="https://images.unsplash.com/photo-..."
                      class="w-full font-mono text-[11px] rounded-xl border border-slate-800 bg-slate-900 px-3.5 py-2.5 text-slate-300">{{ old('images', "https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=1000\nhttps://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=1000") }}</textarea>
        </div>

        <!-- Card 4: Dynamic Technical Specifications Builder -->
        <div class="bg-slate-950 p-6 sm:p-8 rounded-3xl border border-slate-800 shadow-xl space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-850">
                <h3 class="font-bold text-white uppercase tracking-wider text-xs">
                    Technical Specifications Table
                </h3>
                <button type="button" @click="addSpec()" class="text-indigo-400 font-bold hover:underline">
                    + Add Attribute Row
                </button>
            </div>

            <div class="space-y-3">
                <template x-for="(s, index) in specs" :key="index">
                    <div class="flex items-center gap-3">
                        <input type="text" name="spec_keys[]" x-model="s.key" placeholder="Attribute (e.g. Battery)"
                               class="w-1/3 rounded-xl border border-slate-800 bg-slate-900 px-3 py-2 text-white">
                        <input type="text" name="spec_values[]" x-model="s.value" placeholder="Value (e.g. 45 Hours)"
                               class="flex-1 rounded-xl border border-slate-800 bg-slate-900 px-3 py-2 text-white">
                        <button type="button" @click="removeSpec(index)" class="text-rose-400 hover:text-rose-300 text-sm px-2">
                            &times;
                        </button>
                    </div>
                </template>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end gap-3 pt-4">
            <a href="{{ route('vendor.products.index') }}" class="px-5 py-2.5 text-slate-400 hover:text-white font-medium">Cancel</a>
            <button type="submit" class="py-3 px-8 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold uppercase tracking-wider shadow-xl shadow-indigo-600/30 transition-all active:scale-95">
                Publish Product
            </button>
        </div>

    </form>
</div>
@endsection
