@extends('layouts.vendor')

@section('page-title', 'Edit ' . $product->name)

@section('content')
@php
    $specList = [];
    if (is_array($product->specifications)) {
        foreach ($product->specifications as $k => $v) {
            $specList[] = ['key' => $k, 'value' => (string) $v];
        }
    }
    if (empty($specList)) {
        $specList = [
            ['key' => 'Warranty', 'value' => '1 Year Manufacturer']
        ];
    }
@endphp

<div class="max-w-4xl mx-auto space-y-6" x-data="{
    specs: {{ json_encode($specList) }},
    existingImages: {{ json_encode($product->images_with_urls) }},
    uploadedFiles: [],
    isDragging: false,
    showUrlInput: false,
    addSpec() { this.specs.push({ key: '', value: '' }); },
    removeSpec(idx) { this.specs.splice(idx, 1); },
    removeExisting(idx) {
        this.existingImages.splice(idx, 1);
    },
    handleFileSelect(event) {
        this.addFiles(event.target.files);
    },
    handleDrop(event) {
        this.isDragging = false;
        if (event.dataTransfer && event.dataTransfer.files.length) {
            this.addFiles(event.dataTransfer.files);
        }
    },
    addFiles(fileList) {
        const dt = new DataTransfer();
        this.uploadedFiles.forEach(f => dt.items.add(f.file));
        for (let i = 0; i < fileList.length; i++) {
            const file = fileList[i];
            if (file.type.startsWith('image/')) {
                dt.items.add(file);
                this.uploadedFiles.push({
                    id: Math.random().toString(36).substring(2),
                    name: file.name,
                    size: (file.size / 1024 / 1024).toFixed(2) + ' MB',
                    url: URL.createObjectURL(file),
                    file: file
                });
            }
        }
        this.$refs.fileInput.files = dt.files;
    },
    removeFile(idx) {
        this.uploadedFiles.splice(idx, 1);
        const dt = new DataTransfer();
        this.uploadedFiles.forEach(f => dt.items.add(f.file));
        this.$refs.fileInput.files = dt.files;
    }
}">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-black text-white tracking-tight">Edit Product Specifications</h2>
            <p class="text-xs text-slate-500 mt-0.5">Manage photos from your device, update inventory, and refine specifications</p>
        </div>
        <a href="{{ route('vendor.products.index') }}" class="text-xs text-slate-400 hover:text-white transition">
            &larr; Back to Catalog
        </a>
    </div>

    <form action="{{ route('vendor.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6 text-xs">
        @csrf
        @method('PUT')

        @if($errors->any())
            <div class="p-4 rounded-2xl bg-rose-950/60 border border-rose-800 text-rose-300">
                <p class="font-bold mb-1">Please correct the following errors:</p>
                <ul class="list-disc pl-5 space-y-0.5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Core Details -->
        <div class="bg-slate-950 p-6 sm:p-8 rounded-3xl border border-slate-800 shadow-xl space-y-4">
            <h3 class="font-bold text-white uppercase tracking-wider text-xs pb-3 border-b border-slate-850">
                Core Metadata
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block font-bold text-slate-300 mb-1">Product Title</label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                           class="w-full rounded-xl border border-slate-800 bg-slate-900 px-3.5 py-2.5 text-white focus:border-indigo-500 transition">
                </div>

                <div>
                    <label class="block font-bold text-slate-300 mb-1">Category</label>
                    <select name="category_id" required class="w-full rounded-xl border border-slate-800 bg-slate-900 px-3.5 py-2.5 text-white focus:border-indigo-500 transition">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-300 mb-1">SKU</label>
                    <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" required
                           class="w-full font-mono rounded-xl border border-slate-800 bg-slate-900 px-3.5 py-2.5 text-white uppercase focus:border-indigo-500 transition">
                </div>

                <div>
                    <label class="block font-bold text-slate-300 mb-1">Retail Price ($)</label>
                    <input type="number" step="0.01" name="price" value="{{ old('price', $product->price) }}" required
                           class="w-full rounded-xl border border-slate-800 bg-slate-900 px-3.5 py-2.5 text-white focus:border-indigo-500 transition">
                </div>

                <div>
                    <label class="block font-bold text-slate-300 mb-1">Sale Price ($)</label>
                    <input type="number" step="0.01" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}" placeholder="Leave empty if not on sale"
                           class="w-full rounded-xl border border-slate-800 bg-slate-900 px-3.5 py-2.5 text-white focus:border-indigo-500 transition">
                </div>

                <div>
                    <label class="block font-bold text-slate-300 mb-1">Stock Quantity</label>
                    <input type="number" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" required min="0"
                           class="w-full rounded-xl border border-slate-800 bg-slate-900 px-3.5 py-2.5 text-white focus:border-indigo-500 transition">
                </div>

                <div class="flex items-center gap-4 pt-5">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }} class="rounded border-slate-700 bg-slate-900 text-indigo-600 focus:ring-0">
                        <span class="text-white font-medium">Active (Visible in Catalog)</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }} class="rounded border-slate-700 bg-slate-900 text-indigo-600 focus:ring-0">
                        <span class="text-white font-medium">Featured on Homepage</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Descriptions -->
        <div class="bg-slate-950 p-6 sm:p-8 rounded-3xl border border-slate-800 shadow-xl space-y-4">
            <h3 class="font-bold text-white uppercase tracking-wider text-xs pb-3 border-b border-slate-850">
                Descriptions & Copy
            </h3>

            <div>
                <label class="block font-bold text-slate-300 mb-1">Short Description</label>
                <input type="text" name="short_description" value="{{ old('short_description', $product->short_description) }}" placeholder="Short elevator pitch..."
                       class="w-full rounded-xl border border-slate-800 bg-slate-900 px-3.5 py-2.5 text-white focus:border-indigo-500 transition">
            </div>

            <div>
                <label class="block font-bold text-slate-300 mb-1">Full Description</label>
                <textarea name="description" rows="5" required placeholder="Comprehensive product specifications..."
                          class="w-full rounded-xl border border-slate-800 bg-slate-900 px-3.5 py-2.5 text-white focus:border-indigo-500 transition">{{ old('description', $product->description) }}</textarea>
            </div>
        </div>

        <!-- Product Pictures & Device Upload -->
        <div class="bg-slate-950 p-6 sm:p-8 rounded-3xl border border-slate-800 shadow-xl space-y-5">
            <div class="flex items-center justify-between pb-3 border-b border-slate-850">
                <div>
                    <h3 class="font-bold text-white uppercase tracking-wider text-xs">
                        Product Pictures & Media
                    </h3>
                    <p class="text-slate-500 text-[11px] mt-0.5">Manage existing photos or upload fresh pictures directly from your device</p>
                </div>
                <button type="button" @click="showUrlInput = !showUrlInput" class="text-[11px] text-indigo-400 hover:text-indigo-300 font-semibold flex items-center gap-1 transition">
                    <span x-text="showUrlInput ? 'Hide URL input' : 'Or add via URLs'"></span>
                    <svg class="w-3.5 h-3.5 transition-transform" :class="showUrlInput ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
            </div>

            <!-- Existing Pictures Gallery -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block font-bold text-slate-300 text-[11px] uppercase tracking-wider">
                        Current Product Pictures (<span x-text="existingImages.length"></span>)
                    </label>
                    <span class="text-[10px] text-slate-500">Click &times; to remove an image</span>
                </div>

                <template x-if="existingImages.length === 0">
                    <div class="p-4 rounded-xl border border-slate-800 bg-slate-900/40 text-center text-slate-500 text-xs">
                        No pictures currently assigned. Upload pictures from your device below.
                    </div>
                </template>

                <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-5 gap-3" x-show="existingImages.length > 0">
                    <template x-for="(img, idx) in existingImages" :key="img.raw">
                        <div class="relative group rounded-xl overflow-hidden border border-slate-800 bg-slate-900 aspect-square flex flex-col justify-between p-2 transition-all hover:border-slate-700 shadow">
                            <input type="hidden" name="existing_images[]" :value="img.raw">
                            <img :src="img.url" class="absolute inset-0 w-full h-full object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent pointer-events-none"></div>

                            <!-- Header Badge & Remove Button -->
                            <div class="relative z-10 flex items-center justify-between">
                                <template x-if="idx === 0">
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-black bg-indigo-500 text-white tracking-wider shadow">★ HERO</span>
                                </template>
                                <template x-if="idx > 0">
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-semibold bg-slate-900/80 text-slate-300 backdrop-blur" x-text="'#' + (idx + 1)"></span>
                                </template>
                                <button type="button" @click="removeExisting(idx)" class="w-5 h-5 rounded-full bg-rose-500/80 hover:bg-rose-600 text-white flex items-center justify-center text-xs transition shadow active:scale-95 ml-auto" title="Remove picture">
                                    &times;
                                </button>
                            </div>

                            <div class="relative z-10 text-[9px] text-slate-400 font-mono truncate">
                                Saved
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Drag & Drop Zone for New Device Pictures -->
            <div class="pt-3 border-t border-slate-850 space-y-2">
                <label class="block font-bold text-slate-300 text-[11px] uppercase tracking-wider">
                    Add Pictures From Device
                </label>

                <div 
                    @dragover.prevent="isDragging = true"
                    @dragleave.prevent="isDragging = false"
                    @drop.prevent="handleDrop($event)"
                    :class="isDragging ? 'border-indigo-500 bg-indigo-500/10 ring-2 ring-indigo-500/30' : 'border-slate-800 hover:border-slate-700 bg-slate-900/60'"
                    class="relative border-2 border-dashed rounded-2xl p-6 sm:p-7 text-center cursor-pointer transition-all duration-200 group"
                    @click="$refs.fileInput.click()"
                >
                    <input 
                        type="file" 
                        name="image_files[]" 
                        x-ref="fileInput" 
                        @change="handleFileSelect($event)" 
                        multiple 
                        accept="image/png,image/jpeg,image/jpg,image/webp,image/avif,image/gif"
                        class="hidden"
                    >

                    <div class="flex flex-col items-center justify-center space-y-2">
                        <div class="w-10 h-10 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center group-hover:scale-105 group-hover:border-indigo-500/40 transition-transform">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-200 group-hover:text-white transition">
                                <span class="text-indigo-400 underline font-bold">Choose new pictures from device</span> or drag and drop
                            </p>
                            <p class="text-[10px] text-slate-500 mt-0.5">JPEG, PNG, WEBP, AVIF, GIF up to 10MB each</p>
                        </div>
                    </div>
                </div>

                <!-- Live Staged Images Preview Grid -->
                <template x-if="uploadedFiles.length > 0">
                    <div class="space-y-2 pt-2">
                        <div class="flex items-center justify-between">
                            <p class="text-[11px] font-bold text-indigo-400 uppercase tracking-wider">
                                New Pictures to Upload (<span x-text="uploadedFiles.length"></span>)
                            </p>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-5 gap-3">
                            <template x-for="(item, idx) in uploadedFiles" :key="item.id">
                                <div class="relative group rounded-xl overflow-hidden border border-indigo-500/40 bg-slate-900 aspect-square flex flex-col justify-end p-2 transition-all hover:border-indigo-500 hover:shadow-lg">
                                    <img :src="item.url" class="absolute inset-0 w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/20 to-transparent pointer-events-none"></div>

                                    <!-- Top Badges / Remove -->
                                    <div class="absolute top-1.5 left-1.5 right-1.5 flex items-center justify-between z-10">
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-black bg-emerald-500 text-white tracking-wider shadow">NEW</span>
                                        <button type="button" @click.stop="removeFile(idx)" class="w-5 h-5 rounded-full bg-rose-500/80 hover:bg-rose-600 text-white flex items-center justify-center text-xs transition shadow active:scale-95 ml-auto" title="Remove picture">
                                            &times;
                                        </button>
                                    </div>

                                    <!-- Info Footer -->
                                    <div class="relative z-10">
                                        <p class="text-[10px] font-semibold text-white truncate" x-text="item.name"></p>
                                        <p class="text-[9px] text-slate-400 font-mono" x-text="item.size"></p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Optional Collapsible External URL Input -->
            <div x-show="showUrlInput" x-transition.opacity.duration.200ms class="pt-3 border-t border-slate-850 space-y-1.5">
                <label class="block font-bold text-slate-300 text-[11px]">Append External URLs</label>
                <p class="text-slate-500 text-[10px]">Enter extra image URLs (one per line) to append to the gallery.</p>
                <textarea name="images" rows="2" placeholder="https://images.unsplash.com/photo-..."
                          class="w-full font-mono text-[11px] rounded-xl border border-slate-800 bg-slate-900 px-3.5 py-2 text-slate-300 placeholder-slate-600 focus:border-indigo-500 transition">{{ old('images') }}</textarea>
            </div>
        </div>

        <!-- Technical Specifications Builder -->
        <div class="bg-slate-950 p-6 sm:p-8 rounded-3xl border border-slate-800 shadow-xl space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-850">
                <h3 class="font-bold text-white uppercase tracking-wider text-xs">
                    Technical Specifications Table
                </h3>
                <button type="button" @click="addSpec()" class="text-indigo-400 font-bold hover:underline transition">
                    + Add Attribute Row
                </button>
            </div>

            <div class="space-y-3">
                <template x-for="(s, index) in specs" :key="index">
                    <div class="flex items-center gap-3">
                        <input type="text" name="spec_keys[]" x-model="s.key" placeholder="Attribute (e.g. Battery)"
                               class="w-1/3 rounded-xl border border-slate-800 bg-slate-900 px-3 py-2 text-white focus:border-indigo-500 transition">
                        <input type="text" name="spec_values[]" x-model="s.value" placeholder="Value (e.g. 45 Hours)"
                               class="flex-1 rounded-xl border border-slate-800 bg-slate-900 px-3 py-2 text-white focus:border-indigo-500 transition">
                        <button type="button" @click="removeSpec(index)" class="text-rose-400 hover:text-rose-300 text-sm px-2 transition">
                            &times;
                        </button>
                    </div>
                </template>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end gap-3 pt-4">
            <a href="{{ route('vendor.products.index') }}" class="px-5 py-2.5 text-slate-400 hover:text-white font-medium transition">Cancel</a>
            <button type="submit" class="py-3 px-8 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold uppercase tracking-wider shadow-xl shadow-indigo-600/30 transition-all active:scale-95">
                Save Changes
            </button>
        </div>
    </form>
</div>
@endsection
