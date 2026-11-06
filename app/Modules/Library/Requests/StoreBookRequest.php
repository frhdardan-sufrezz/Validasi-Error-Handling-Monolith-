<?php

namespace Modules\Library\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    // Rapikan ISBN (hapus tanda hubung/spasi, huruf X kapital) sebelum divalidasi
    protected function prepareForValidation(): void
    {
        if (is_string($this->isbn)) {
            $this->merge([
                'isbn' => strtoupper(str_replace(['-', ' '], '', $this->isbn)),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'isbn' => ['required', 'string', 'regex:/^(?:\d{9}[\dX]|97[89]\d{10})$/', 'unique:books,isbn'],
            'published_year' => ['required', 'integer', 'min:1900', 'max:' . now()->year],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'author_id' => ['required', 'exists:authors,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul buku wajib diisi.',
            'title.max' => 'Judul buku maksimal 255 karakter.',
            'isbn.required' => 'ISBN wajib diisi.',
            'isbn.regex' => 'Format ISBN tidak valid. Gunakan ISBN-10 atau ISBN-13 (boleh memakai tanda hubung).',
            'isbn.unique' => 'ISBN sudah terdaftar.',
            'published_year.required' => 'Tahun terbit wajib diisi.',
            'published_year.integer' => 'Tahun terbit harus berupa bilangan bulat.',
            'published_year.min' => 'Tahun terbit minimal 1900.',
            'published_year.max' => 'Tahun terbit maksimal ' . now()->year . '.',
            'price.required' => 'Harga wajib diisi.',
            'price.numeric' => 'Harga harus berupa angka.',
            'price.min' => 'Harga tidak boleh negatif.',
            'stock.required' => 'Stok wajib diisi.',
            'stock.integer' => 'Stok harus berupa bilangan bulat.',
            'stock.min' => 'Stok tidak boleh negatif.',
            'author_id.required' => 'Penulis wajib dipilih.',
            'author_id.exists' => 'Penulis yang dipilih tidak valid.',
        ];
    }
}
