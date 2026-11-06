@extends('layouts.admin')

@section('title', 'Categories & Hierarchy')

@section('content')
<div class="space-y-6" x-data="{ addModal: false, editModal: false, editingCat: {} }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-white tracking-tight flex items-center gap-3">
                Categories & Taxonomy
                <span class="text-xs font-mono font-semibold px-2.5 py-1 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/20">
                    {{ $categories->count() }} Categories
                </span>
            </h1>
            <p class="text-slate-400 text-xs mt-1">Organize marketplace categories, sub-categories, hierarchy, and header navigation tiles.</p>
        </div>

        <button @click="addModal = true" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-bold shadow-lg shadow-amber-500/20 flex items-center gap-2 transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Category
        </button>
    </div>

    <!-- Categories Grid / Table -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-800 bg-slate-950/50 text-slate-400 uppercase tracking-wider font-mono text-[10px]">
                        <th class="py-3 px-4">Category</th>
                        <th class="py-3 px-4">Slug</th>
                        <th class="py-3 px-4">Products</th>
                        <th class="py-3 px-4">Order</th>
                        <th class="py-3 px-4">Parent Category</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($categories as $category)
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $category->image ?? 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=100' }}" alt="{{ $category->name }}" class="w-10 h-10 rounded-xl object-cover bg-slate-800 flex-shrink-0">
                                    <div>
                                        <div class="font-bold text-white text-sm">{{ $category->name }}</div>
                                        <div class="text-[11px] text-slate-400 line-clamp-1">{{ $category->description ?? 'No description' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 font-mono text-slate-400">
                                /category/{{ $category->slug }}
                            </td>
                            <td class="py-3.5 px-4 font-mono font-bold text-amber-400">
                                {{ $category->products_count }} items
                            </td>
                            <td class="py-3.5 px-4 font-mono text-slate-300">
                                #{{ $category->sort_order }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-400">
                                {{ $category->parent ? $category->parent->name : 'Root Level' }}
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <!-- Edit Trigger -->
                                    <button @click="editingCat = { id: {{ $category->id }}, name: '{{ addslashes($category->name) }}', description: '{{ addslashes($category->description ?? '') }}', image: '{{ addslashes($category->image ?? '') }}', icon: '{{ addslashes($category->icon ?? '') }}', parent_id: '{{ $category->parent_id ?? '' }}', sort_order: {{ $category->sort_order }} }; editModal = true" class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition" title="Edit Category">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    </button>

                                    <!-- Delete Category -->
                                    <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Delete category {{ $category->name }}? Products in this category will become unassigned.')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg bg-slate-800 hover:bg-red-950/60 text-slate-400 hover:text-red-400 transition" title="Delete Category">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-500">No categories created yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create Category Modal -->
    <div x-show="addModal" x-transition.opacity class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;">
        <div @click.away="addModal = false" class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-lg p-6 shadow-2xl relative">
            <h2 class="text-lg font-bold text-white mb-1">Create Product Category</h2>
            <p class="text-xs text-slate-400 mb-4">Add a new taxonomy tier for marketplace catalog items.</p>

            <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Category Title</label>
                    <input type="text" name="name" required placeholder="e.g. Ergonomic Hardware, Audio Engineering..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Description</label>
                    <textarea name="description" rows="2" placeholder="Brief description of the collection..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500"></textarea>
                </div>

                <div class="space-y-2">
                    <label class="block text-xs font-semibold text-slate-300">Category Cover Picture</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <span class="block text-[11px] text-slate-400 mb-1">From Device</span>
                            <input type="file" name="image_file" accept="image/*" class="w-full text-xs text-slate-400 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-amber-500/10 file:text-amber-400 hover:file:bg-amber-500/20 bg-slate-950 border border-slate-800 rounded-xl">
                        </div>
                        <div>
                            <span class="block text-[11px] text-slate-400 mb-1">Or via Image URL</span>
                            <input type="url" name="image" placeholder="https://..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-1.5 text-xs text-white focus:border-amber-500">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Parent Hierarchy (Optional)</label>
                        <select name="parent_id" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                            <option value="">None (Top-Level)</option>
                            @foreach($categories as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Sort Priority Order</label>
                        <input type="number" name="sort_order" value="0" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 font-mono">
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-3 border-t border-slate-800">
                    <button type="button" @click="addModal = false" class="px-4 py-2 rounded-xl border border-slate-800 text-slate-400 hover:text-white text-xs font-medium">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-bold">Publish Category</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div x-show="editModal" x-transition.opacity class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;">
        <div @click.away="editModal = false" class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-lg p-6 shadow-2xl relative">
            <h2 class="text-lg font-bold text-white mb-1">Edit Category Details</h2>
            <p class="text-xs text-slate-400 mb-4">Modify category properties, visuals, or sort rank.</p>

            <form :action="'{{ url('admin/categories') }}/' + editingCat.id" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Category Title</label>
                    <input type="text" name="name" x-model="editingCat.name" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Description</label>
                    <textarea name="description" x-model="editingCat.description" rows="2" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500"></textarea>
                </div>

                <div class="space-y-2">
                    <label class="block text-xs font-semibold text-slate-300">Update Cover Picture</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <span class="block text-[11px] text-slate-400 mb-1">Upload New Picture</span>
                            <input type="file" name="image_file" accept="image/*" class="w-full text-xs text-slate-400 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-amber-500/10 file:text-amber-400 hover:file:bg-amber-500/20 bg-slate-950 border border-slate-800 rounded-xl">
                        </div>
                        <div>
                            <span class="block text-[11px] text-slate-400 mb-1">Or Image URL</span>
                            <input type="url" name="image" x-model="editingCat.image" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-1.5 text-xs text-white focus:border-amber-500">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Sort Priority Order</label>
                    <input type="number" name="sort_order" x-model="editingCat.sort_order" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 font-mono">
                </div>

                <div class="flex justify-end gap-3 pt-3 border-t border-slate-800">
                    <button type="button" @click="editModal = false" class="px-4 py-2 rounded-xl border border-slate-800 text-slate-400 hover:text-white text-xs font-medium">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-bold">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
