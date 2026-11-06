<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Library\Models\Author;
use Modules\Library\Models\Book;

/**
 * Data contoh untuk pengujian manual (aman dijalankan berulang kali).
 *
 * Jalankan: php artisan db:seed --class=LibrarySeeder
 */
class LibrarySeeder extends Seeder
{
    public function run(): void
    {
        $budi = Author::firstOrCreate(
            ['email' => 'budi@example.com'],
            ['name' => 'Budi Santoso', 'bio' => 'Penulis buku pemrograman web.']
        );

        $sari = Author::firstOrCreate(
            ['email' => 'sari@example.com'],
            ['name' => 'Sari Wulandari', 'bio' => 'Penulis buku basis data.']
        );

        // Penulis TANPA buku -> dipakai untuk menguji hapus penulis yang berhasil
        Author::firstOrCreate(
            ['email' => 'tanpa.buku@example.com'],
            ['name' => 'Penulis Tanpa Buku', 'bio' => null]
        );

        $books = [
            ['isbn' => '9780306406157', 'title' => 'Belajar Laravel 13', 'published_year' => 2025, 'price' => 85000, 'stock' => 10, 'author_id' => $budi->id],
            ['isbn' => '9780131103627', 'title' => 'Dasar Pemrograman PHP', 'published_year' => 2021, 'price' => 65000, 'stock' => 3, 'author_id' => $budi->id],
            ['isbn' => '9781593279509', 'title' => 'Basis Data untuk Pemula', 'published_year' => 2019, 'price' => 70000, 'stock' => 5, 'author_id' => $sari->id],
            // Stok 0 -> dipakai untuk menguji BookNotAvailableException
            ['isbn' => '0306406152', 'title' => 'Buku Stok Habis', 'published_year' => 2018, 'price' => 50000, 'stock' => 0, 'author_id' => $sari->id],
        ];

        foreach ($books as $data) {
            Book::firstOrCreate(['isbn' => $data['isbn']], $data);
        }
    }
}
