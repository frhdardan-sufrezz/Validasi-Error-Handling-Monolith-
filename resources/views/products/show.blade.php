<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Produk') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <dl class="space-y-2">
                        <div><dt class="font-semibold">Nama</dt><dd>{{ $product->name }}</dd></div>
                        <div><dt class="font-semibold">Slug</dt><dd>{{ $product->slug }}</dd></div>
                        <div><dt class="font-semibold">Harga</dt><dd>Rp {{ number_format($product->price, 2, ',', '.') }}</dd></div>
                        <div><dt class="font-semibold">Stok</dt><dd>{{ $product->stock }}</dd></div>
                        <div><dt class="font-semibold">Deskripsi</dt><dd>{{ $product->description ?? '-' }}</dd></div>
                    </dl>

                    <hr class="my-6">

                    <h3 class="font-semibold mb-2">Kurangi Stok (untuk uji Custom Exception)</h3>
                    <form action="{{ route('products.reduce-stock', $product) }}" method="POST" class="flex items-center space-x-2">
                        @csrf
                        <input type="number" name="quantity" value="{{ old('quantity', 1) }}" min="1"
                               class="rounded-md border-gray-300 shadow-sm w-32">
                        <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                            Kurangi
                        </button>
                    </form>

                    <div class="mt-6">
                        <a href="{{ route('products.index') }}" class="text-gray-600 hover:text-gray-800">&larr; Kembali</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
