@php($book = $book ?? null)

<div class="mb-4">
    <label for="title" class="block text-sm font-medium text-gray-700">Judul</label>
    <input type="text" name="title" id="title" value="{{ old('title', $book?->title) }}"
           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
    @error('title')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mb-4">
    <label for="isbn" class="block text-sm font-medium text-gray-700">ISBN</label>
    <input type="text" name="isbn" id="isbn" value="{{ old('isbn', $book?->isbn) }}" placeholder="9780306406157"
           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
    @error('isbn')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mb-4">
    <label for="published_year" class="block text-sm font-medium text-gray-700">Tahun Terbit</label>
    <input type="text" name="published_year" id="published_year" value="{{ old('published_year', $book?->published_year) }}"
           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
    @error('published_year')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mb-4">
    <label for="price" class="block text-sm font-medium text-gray-700">Harga</label>
    <input type="text" name="price" id="price" value="{{ old('price', $book?->price) }}"
           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
    @error('price')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mb-4">
    <label for="stock" class="block text-sm font-medium text-gray-700">Stok</label>
    <input type="text" name="stock" id="stock" value="{{ old('stock', $book?->stock) }}"
           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
    @error('stock')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mb-4">
    <label for="author_id" class="block text-sm font-medium text-gray-700">Penulis</label>
    <select name="author_id" id="author_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        <option value="">-- Pilih Penulis --</option>
        @foreach ($authors as $author)
            <option value="{{ $author->id }}" @selected((string) old('author_id', $book?->author_id) === (string) $author->id)>
                {{ $author->name }}
            </option>
        @endforeach
    </select>
    @error('author_id')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>
