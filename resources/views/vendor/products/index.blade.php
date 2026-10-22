@extends('layouts.vendor')

@section('page-title', 'Manage Products & Stock')

@section('content')
<div class="space-y-6" x-data="{ bulkModal: false }">

    <!-- Top Action Toolbar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <form action="{{ route('vendor.products.index') }}" method="GET" class="flex items-center gap-3 flex-1 max-w-md">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search products by title or SKU..."
                   class="w-full text-xs rounded-xl border border-slate-800 bg-slate-950 px-3.5 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </form>

        <div class="flex items-center gap-3">
            <button @click="bulkModal = true" class="py-2.5 px-4 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs uppercase tracking-wider flex items-center gap-2 transition-colors">
                <span>📁 CSV Bulk Upload</span>
            </button>
            <a href="{{ route('vendor.products.create') }}" class="py-2.5 px-5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs uppercase tracking-wider shadow-lg shadow-indigo-600/30 transition-all flex items-center gap-2 active:scale-95">
                <span>+ Add Product</span>
            </a>
        </div>
    </div>

    <!-- Products Table -->
    <div class="bg-slate-950 rounded-3xl border border-slate-800 shadow-xl overflow-x-auto">
        <table class="w-full text-xs text-left min-w-[750px]">
            <thead>
                <tr class="border-b border-slate-850 text-slate-400 uppercase font-mono tracking-wider text-[10px] bg-slate-900/60">
                    <th class="p-4">Product</th>
                    <th class="p-4">Category</th>
                    <th class="p-4">Price</th>
                    <th class="p-4">Inventory</th>
                    <th class="p-4">Active</th>
                    <th class="p-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-850">
                @forelse($products as $prod)
                    <tr class="hover:bg-slate-900/40 transition-colors">
                        <td class="p-4">
                            <div class="flex items-center gap-3">
                                <img src="{{ $prod->featured_image }}" alt="" class="w-12 h-12 rounded-xl object-cover border border-slate-800 flex-shrink-0">
                                <div>
                                    <h4 class="font-bold text-white text-xs line-clamp-1">
                                        <a href="{{ route('products.show', $prod->slug) }}" target="_blank" class="hover:text-indigo-400">
                                            {{ $prod->name }}
                                        </a>
                                    </h4>
                                    <span class="text-[10px] font-mono text-slate-400">SKU: {{ $prod->sku }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="p-4 text-slate-300 font-medium">
                            {{ $prod->category->name ?? 'General' }}
                        </td>
                        <td class="p-4">
                            <div class="font-bold text-white">${{ number_format($prod->current_price, 2) }}</div>
                            @if($prod->sale_price)
                                <span class="text-[10px] text-rose-400 font-mono">Sale: ${{ number_format($prod->sale_price, 2) }}</span>
                            @endif
                        </td>
                        <td class="p-4">
                            <span class="px-2 py-0.5 rounded text-[11px] font-bold font-mono {{ $prod->stock_quantity <= 5 ? 'bg-amber-950 text-amber-400' : 'bg-emerald-950 text-emerald-400' }}">
                                {{ $prod->stock_quantity }} units
                            </span>
                        </td>
                        <td class="p-4">
                            <form action="{{ route('vendor.products.toggle', $prod->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-10 h-6 rounded-full transition-colors relative p-0.5 {{ $prod->is_active ? 'bg-indigo-600' : 'bg-slate-800' }}">
                                    <div class="w-5 h-5 rounded-full bg-white transition-transform {{ $prod->is_active ? 'translate-x-4' : 'translate-x-0' }}"></div>
                                </button>
                            </form>
                        </td>
                        <td class="p-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('vendor.products.edit', $prod->id) }}" class="p-2 rounded-lg bg-slate-900 text-slate-300 hover:text-white hover:bg-slate-800" title="Edit Product">
                                    ✏️
                                </a>
                                <form action="{{ route('vendor.products.destroy', $prod->id) }}" method="POST" onsubmit="return confirm('Delete this product permanently?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 rounded-lg bg-slate-900 text-rose-400 hover:text-rose-300 hover:bg-slate-800" title="Delete Product">
                                        🗑️
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-xs text-slate-500">
                            No products matching criteria. Click "+ Add Product" to publish your first item!
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $products->links() }}
    </div>

    <!-- Bulk Upload CSV Modal -->
    <div x-show="bulkModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
        <div class="max-w-md w-full bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-800 shadow-2xl space-y-4" @click.outside="bulkModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <h4 class="font-bold text-sm text-white">Bulk Product Importer (CSV)</h4>
                <button @click="bulkModal = false" class="text-slate-400 hover:text-white">&times;</button>
            </div>

            <p class="text-xs text-slate-400 leading-relaxed">
                Upload a structured spreadsheet containing products, inventory counts, and SKUs to rapidly batch populate your merchant catalog.
            </p>

            <div class="p-3 rounded-2xl bg-slate-950 border border-slate-800 text-xs flex items-center justify-between">
                <span class="text-slate-400">Standard Column Template:</span>
                <a href="{{ route('vendor.products.template') }}" class="font-bold text-indigo-400 hover:underline">
                    📥 Download CSV Template
                </a>
            </div>

            <form action="{{ route('vendor.products.bulk') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-300 mb-2">Select CSV File</label>
                    <input type="file" name="csv_file" accept=".csv"
                           class="w-full rounded-xl border border-slate-800 bg-slate-950 p-2.5 text-slate-400">
                    <span class="text-[10px] text-slate-500 mt-1 block">Or submit without a file to import sample test inventory batch items.</span>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" @click="bulkModal = false" class="px-4 py-2 text-slate-400">Cancel</button>
                    <button type="submit" class="py-2.5 px-5 rounded-xl bg-indigo-600 text-white font-bold uppercase tracking-wider hover:bg-indigo-700 shadow-md">
                        Process Batch Import
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
