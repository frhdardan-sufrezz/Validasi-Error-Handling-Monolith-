@php($product = $product ?? null)

<div class="mb-4">
    <label for="name" class="block text-sm font-medium text-gray-700">Nama Produk</label>
    <input type="text" name="name" id="name" value="{{ old('name', $product?->name) }}"
           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
    @error('name')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mb-4">
    <label for="slug" class="block text-sm font-medium text-gray-700">Slug</label>
    <input type="text" name="slug" id="slug" value="{{ old('slug', $product?->slug) }}"
           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
    @error('slug')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mb-4">
    <label for="price" class="block text-sm font-medium text-gray-700">Harga</label>
    <input type="number" step="0.01" name="price" id="price" value="{{ old('price', $product?->price) }}"
           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
    @error('price')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mb-4">
    <label for="stock" class="block text-sm font-medium text-gray-700">Stok</label>
    <input type="number" name="stock" id="stock" value="{{ old('stock', $product?->stock) }}"
           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
    @error('stock')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mb-4">
    <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
    <textarea name="description" id="description" rows="4"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('description', $product?->description) }}</textarea>
    @error('description')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>
