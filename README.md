# Paket Praktikum 5: Modul Library (Validasi & Error Handling)

Isi paket ini sudah menyatu dengan struktur project `auth-monolith` (namespace `Modules\Library\...`).
Salin seluruh isi folder ini ke root project, timpa jika ditanya.

## Isi paket
| Lokasi | Keterangan |
|---|---|
| app/Modules/Library/ | Controller, Model, Request, Exception, Provider, route |
| database/migrations/ | Migrasi `authors` dan `books` |
| database/seeders/LibrarySeeder.php | Data contoh untuk uji manual (ada buku stok 0 dan penulis tanpa buku) |
| resources/views/books, authors, library/ | Semua view (index, create, edit, _form, _alerts) |
| tests/Feature/Library/ | Pest test: validasi buku, validasi penulis, custom exception, log |
| bersihkan.sh / bersihkan.ps1 | Menghapus migrasi produk ganda dan file sisa |

## Langkah menjalankan
1. Salin isi paket ke root project.
2. Bersihkan (WAJIB, kalau tidak `migrate:fresh` dan test gagal karena migrasi produk ganda):
   - Linux/macOS/WSL: `bash bersihkan.sh`
   - Windows PowerShell: `.\bersihkan.ps1`
3. Pastikan bootstrap sudah benar (sudah ada di project Anda):
   - `bootstrap/providers.php` memuat `Modules\Library\Providers\LibraryServiceProvider::class`
   - `bootstrap/app.php` memuat render untuk `BookNotAvailableException` dan `AuthorHasBooksException`
4. `composer dump-autoload`
5. `php artisan migrate:fresh --seed`
6. `php artisan db:seed --class=LibrarySeeder`
7. `php artisan test tests/Feature/Library`
8. `php artisan serve`, buka http://localhost:8000 dan login: `user@example.com` / `password123`

Catatan: rute buku dan penulis dilindungi middleware `auth`, jadi login dulu.
# Validasi-Error-Handling-Monolith-
