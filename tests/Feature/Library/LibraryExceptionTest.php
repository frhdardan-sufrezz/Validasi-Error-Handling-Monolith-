<?php

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Modules\Library\Exceptions\AuthorHasBooksException;
use Modules\Library\Exceptions\BookNotAvailableException;
use Modules\Library\Models\Author;
use Modules\Library\Models\Book;

beforeEach(function () {
    $this->withoutVite();
});

if (! function_exists('exceptionTestAuthor')) {
    function exceptionTestAuthor(string $email = 'budi@example.com'): Author
    {
        return Author::create([
            'name' => 'Budi Santoso',
            'email' => $email,
            'bio' => null,
        ]);
    }
}

if (! function_exists('exceptionTestBook')) {
    function exceptionTestBook(Author $author, int $stock): Book
    {
        return Book::create([
            'author_id' => $author->id,
            'title' => 'Belajar Laravel',
            'isbn' => '9780306406157',
            'published_year' => 2024,
            'price' => 85000,
            'stock' => $stock,
        ]);
    }
}

test('meminjam buku stok 0 memunculkan BookNotAvailableException dan stok tetap 0', function () {
    $user = User::factory()->create();
    $book = exceptionTestBook(exceptionTestAuthor(), 0);

    $this->actingAs($user)
        ->from(route('books.index'))
        ->post(route('books.borrow', $book))
        ->assertRedirect(route('books.index'))
        ->assertSessionHas('error', "Buku 'Belajar Laravel' sedang tidak tersedia (stok 0) sehingga tidak dapat dipinjam.");

    expect($book->fresh()->stock)->toBe(0);
});

test('meminjam buku yang stoknya tersedia mengurangi stok satu', function () {
    $user = User::factory()->create();
    $book = exceptionTestBook(exceptionTestAuthor(), 3);

    $this->actingAs($user)
        ->from(route('books.index'))
        ->post(route('books.borrow', $book))
        ->assertRedirect(route('books.index'))
        ->assertSessionHas('success', "Buku 'Belajar Laravel' berhasil dipinjam.");

    expect($book->fresh()->stock)->toBe(2);
});

test('stok terakhir dapat dipinjam lalu peminjaman berikutnya ditolak', function () {
    $user = User::factory()->create();
    $book = exceptionTestBook(exceptionTestAuthor(), 1);

    $this->actingAs($user)->from(route('books.index'))->post(route('books.borrow', $book))
        ->assertSessionHas('success');
    expect($book->fresh()->stock)->toBe(0);

    $this->actingAs($user)->from(route('books.index'))->post(route('books.borrow', $book))
        ->assertSessionHas('error');
    expect($book->fresh()->stock)->toBe(0);
});

test('request json untuk buku stok 0 mengembalikan 422 dengan detail', function () {
    $user = User::factory()->create();
    $book = exceptionTestBook(exceptionTestAuthor(), 0);

    $this->actingAs($user)
        ->postJson(route('books.borrow', $book))
        ->assertStatus(422)
        ->assertJson([
            'error' => 'book_not_available',
            'details' => ['book_id' => $book->id, 'book_title' => 'Belajar Laravel'],
        ]);
});

test('menghapus penulis yang masih punya buku memunculkan AuthorHasBooksException', function () {
    $user = User::factory()->create();
    $author = exceptionTestAuthor();
    exceptionTestBook($author, 5);

    $this->actingAs($user)
        ->from(route('authors.index'))
        ->delete(route('authors.destroy', $author))
        ->assertRedirect(route('authors.index'))
        ->assertSessionHas('error', "Penulis 'Budi Santoso' tidak dapat dihapus karena masih memiliki 1 buku.");

    $this->assertDatabaseHas('authors', ['id' => $author->id]);
});

test('request json untuk hapus penulis yang punya buku mengembalikan 422', function () {
    $user = User::factory()->create();
    $author = exceptionTestAuthor();
    exceptionTestBook($author, 5);

    $this->actingAs($user)
        ->deleteJson(route('authors.destroy', $author))
        ->assertStatus(422)
        ->assertJson([
            'error' => 'author_has_books',
            'details' => ['author' => 'Budi Santoso', 'book_count' => 1],
        ]);
});

test('penulis tanpa buku dapat dihapus', function () {
    $user = User::factory()->create();
    $author = exceptionTestAuthor();

    $this->actingAs($user)
        ->delete(route('authors.destroy', $author))
        ->assertRedirect(route('authors.index'))
        ->assertSessionHas('success', 'Penulis berhasil dihapus.');

    $this->assertDatabaseMissing('authors', ['id' => $author->id]);
});

test('BookNotAvailableException mencatat peringatan ke log', function () {
    Log::spy();

    (new BookNotAvailableException('Belajar Laravel', 7))->report();

    Log::shouldHaveReceived('warning')
        ->once()
        ->withArgs(fn ($message, $context = []) => str_contains($message, 'BookNotAvailableException')
            && ($context['book_id'] ?? null) === 7);
});

test('AuthorHasBooksException mencatat peringatan ke log', function () {
    Log::spy();

    (new AuthorHasBooksException('Budi Santoso', 2))->report();

    Log::shouldHaveReceived('warning')
        ->once()
        ->withArgs(fn ($message, $context = []) => str_contains($message, 'AuthorHasBooksException')
            && ($context['book_count'] ?? null) === 2);
});
