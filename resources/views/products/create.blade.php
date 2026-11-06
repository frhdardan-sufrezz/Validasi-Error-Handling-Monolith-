<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Produk') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('products.store') }}" method="POST">
                        @csrf
                        @include('products._form')

                        <button type="submit" style="background:#3b82f6;color:#fff;padding:8px 16px;border-radius:6px;font-weight:bold;">
                            Simpan
                        </button>
                        </button>
                        <a href="{{ route('products.index') }}" class="ml-3 text-gray-600 hover:text-gray-800">Batal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>