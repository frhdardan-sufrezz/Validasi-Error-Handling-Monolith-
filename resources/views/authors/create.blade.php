<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Tambah Penulis') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @include('library._alerts')

                    <form action="{{ route('authors.store') }}" method="POST">
                        @csrf
                        @include('authors._form')

                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Simpan</button>
                        <a href="{{ route('authors.index') }}" class="ml-3 text-gray-600 hover:text-gray-800">Batal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
