<?php

use App\Models\User;
use Modules\Library\Models\Author;
use Modules\Library\Models\Book;

beforeEach(function () {
    $this->withoutVite();
});

if (! function_exists('bookTestAuthor')) {
    function bookTestAuthor(): Author
    {
        return Author::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'bio' => null,
        ]);
    }
}

if (! function_exists('bookTestPayload')) {
    function bookTestPayload(int $authorId, array $override = []): array
    {
        return array_merge([
            'title' => 'Pemrograman Laravel',
            'isbn' => '9780306406157',
            'published_year' => 2020,
            'price' => 75000,
            'stock' => 5,
            'author_id' => $authorId,
        ], $override);
    }
}

test('tamu diarahkan ke halaman login', function () {
    $this->get(route('books.index'))->assertRedirect(route('login'));
});

test('halaman daftar dan form buku dapat dibuka', function () {
    $author = bookTestAuthor();
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('books.index'))->assertOk();
    $this->actingAs($user)->get(route('books.create'))->assertOk()->assertSee($author->name);
});

test('form buku kosong menampilkan semua pesan wajib diisi dalam bahasa indonesia', function () {
    bookTestAuthor();
    $user = User::factory()->create();

    $this->actingAs($user)
        ->from(route('books.create'))
        ->post(route('books.store'), [])
        ->assertRedirect(route('books.create'))
        ->assertSessionHasErrors([
            'title' => 'Judul buku wajib diisi.',
            'isbn' => 'ISBN wajib diisi.',
            'published_year' => 'Tahun terbit wajib diisi.',
            'price' => 'Harga wajib diisi.',
            'stock' => 'Stok wajib diisi.',
            'author_id' => 'Penulis wajib dipilih.',
        ]);

    expect(Book::count())->toBe(0);
});

test('data buku tidak valid ditolak', function (array $override, string $field) {
    $author = bookTestAuthor();
    $user = User::factory()->create();

    $this->actingAs($user)
        ->from(route('books.create'))
        ->post(route('books.store'), bookTestPayload($author->id, $override))
        ->assertRedirect(route('books.create'))
        ->assertSessionHasErrors($field);

    expect(Book::count())->toBe(0);
})->with([
    'judul lebih dari 255 karakter' => [['title' => str_repeat('a', 256)], 'title'],
    'format isbn salah' => [['isbn' => 'abc123'], 'isbn'],
    'isbn kurang digit' => [['isbn' => '12345'], 'isbn'],
    'tahun di bawah 1900' => [['published_year' => 1899], 'published_year'],
    'tahun melebihi tahun sekarang' => [['published_year' => 9999], 'published_year'],
    'tahun bukan angka' => [['published_year' => 'abcd'], 'published_year'],
    'harga negatif' => [['price' => -1], 'price'],
    'harga bukan angka' => [['price' => 'abc'], 'price'],
    'stok negatif' => [['stock' => -5], 'stock'],
    'stok desimal' => [['stock' => '1.5'], 'stock'],
    'penulis tidak ada' => [['author_id' => 9999], 'author_id'],
]);

test('pesan error spesifik berbahasa indonesia', function () {
    $author = bookTestAuthor();
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('books.store'), bookTestPayload($author->id, [
            'isbn' => 'abc123',
            'published_year' => 1800,
            'price' => -1,
            'stock' => -5,
            'author_id' => 9999,
        ]))
        ->assertSessionHasErrors([
            'isbn' => 'Format ISBN tidak valid. Gunakan ISBN-10 atau ISBN-13 (boleh memakai tanda hubung).',
            'published_year' => 'Tahun terbit minimal 1900.',
            'price' => 'Harga tidak boleh negatif.',
            'stock' => 'Stok tidak boleh negatif.',
            'author_id' => 'Penulis yang dipilih tidak valid.',
        ]);
});

test('isbn duplikat ditolak termasuk yang ditulis dengan tanda hubung', function () {
    $author = bookTestAuthor();
    $user = User::factory()->create();

    Book::create(bookTestPayload($author->id));

    $this->actingAs($user)
        ->post(route('books.store'), bookTestPayload($author->id, ['isbn' => '978-0-306-40615-7']))
        ->assertSessionHasErrors(['isbn' => 'ISBN sudah terdaftar.']);

    expect(Book::count())->toBe(1);
});

test('buku dengan data valid tersimpan dan isbn dirapikan', function () {
    $author = bookTestAuthor();
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('books.store'), bookTestPayload($author->id, ['isbn' => '978-0-306-40615-7']))
        ->assertRedirect(route('books.index'))
        ->assertSessionHas('success', 'Buku berhasil ditambahkan.');

    $this->assertDatabaseHas('books', [
        'title' => 'Pemrograman Laravel',
        'isbn' => '9780306406157',
        'author_id' => $author->id,
        'stock' => 5,
    ]);
});

test('isbn-10 dengan digit pemeriksa x diterima', function () {
    $author = bookTestAuthor();
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('books.store'), bookTestPayload($author->id, ['isbn' => '080442957x']))
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('books', ['isbn' => '080442957X']);
});

test('update buku boleh memakai isbn miliknya sendiri tetapi tidak isbn buku lain', function () {
    $author = bookTestAuthor();
    $user = User::factory()->create();

    $first = Book::create(bookTestPayload($author->id));
    $second = Book::create(bookTestPayload($author->id, ['isbn' => '9780131103627', 'title' => 'Dasar PHP']));

    $this->actingAs($user)
        ->put(route('books.update', $first), bookTestPayload($author->id, ['title' => 'Judul Baru']))
        ->assertRedirect(route('books.index'))
        ->assertSessionHas('success', 'Buku berhasil diperbarui.');

    $this->assertDatabaseHas('books', ['id' => $first->id, 'title' => 'Judul Baru']);

    $this->actingAs($user)
        ->put(route('books.update', $second), bookTestPayload($author->id, ['isbn' => '9780306406157']))
        ->assertSessionHasErrors(['isbn' => 'ISBN sudah terdaftar.']);
});

test('buku dapat dihapus', function () {
    $author = bookTestAuthor();
    $user = User::factory()->create();
    $book = Book::create(bookTestPayload($author->id));

    $this->actingAs($user)
        ->delete(route('books.destroy', $book))
        ->assertRedirect(route('books.index'))
        ->assertSessionHas('success', 'Buku berhasil dihapus.');

    $this->assertDatabaseMissing('books', ['id' => $book->id]);
});
