<?php

use App\Models\User;
use Modules\Library\Models\Author;

beforeEach(function () {
    $this->withoutVite();
});

if (! function_exists('authorTestPayload')) {
    function authorTestPayload(array $override = []): array
    {
        return array_merge([
            'name' => 'Sari Wulandari',
            'email' => 'sari@example.com',
            'bio' => 'Penulis buku basis data.',
        ], $override);
    }
}

test('halaman daftar dan form penulis dapat dibuka', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('authors.index'))->assertOk();
    $this->actingAs($user)->get(route('authors.create'))->assertOk();
});

test('form penulis kosong menampilkan pesan wajib diisi', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->from(route('authors.create'))
        ->post(route('authors.store'), [])
        ->assertRedirect(route('authors.create'))
        ->assertSessionHasErrors([
            'name' => 'Nama penulis wajib diisi.',
            'email' => 'Email wajib diisi.',
        ]);

    expect(Author::count())->toBe(0);
});

test('data penulis tidak valid ditolak', function (array $override, string $field, string $message) {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('authors.store'), authorTestPayload($override))
        ->assertSessionHasErrors([$field => $message]);

    expect(Author::count())->toBe(0);
})->with([
    'nama lebih dari 255 karakter' => [['name' => str_repeat('a', 256)], 'name', 'Nama penulis maksimal 255 karakter.'],
    'format email salah' => [['email' => 'budi@'], 'email', 'Format email tidak valid.'],
    'bio lebih dari 1000 karakter' => [['bio' => str_repeat('a', 1001)], 'bio', 'Bio maksimal 1000 karakter.'],
]);

test('email duplikat ditolak', function () {
    $user = User::factory()->create();
    Author::create(authorTestPayload());

    $this->actingAs($user)
        ->post(route('authors.store'), authorTestPayload(['name' => 'Orang Lain']))
        ->assertSessionHasErrors(['email' => 'Email sudah digunakan penulis lain.']);

    expect(Author::count())->toBe(1);
});

test('penulis dengan data valid tersimpan dan bio boleh kosong', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('authors.store'), authorTestPayload(['bio' => null]))
        ->assertRedirect(route('authors.index'))
        ->assertSessionHas('success', 'Penulis berhasil ditambahkan.');

    $this->assertDatabaseHas('authors', ['email' => 'sari@example.com', 'name' => 'Sari Wulandari']);
});

test('update penulis boleh memakai email miliknya sendiri tetapi tidak email penulis lain', function () {
    $user = User::factory()->create();
    $first = Author::create(authorTestPayload());
    $second = Author::create(authorTestPayload(['name' => 'Budi', 'email' => 'budi@example.com']));

    $this->actingAs($user)
        ->put(route('authors.update', $first), authorTestPayload(['name' => 'Nama Baru']))
        ->assertRedirect(route('authors.index'))
        ->assertSessionHas('success', 'Penulis berhasil diperbarui.');

    $this->assertDatabaseHas('authors', ['id' => $first->id, 'name' => 'Nama Baru']);

    $this->actingAs($user)
        ->put(route('authors.update', $second), authorTestPayload(['email' => 'sari@example.com']))
        ->assertSessionHasErrors(['email' => 'Email sudah digunakan penulis lain.']);
});
