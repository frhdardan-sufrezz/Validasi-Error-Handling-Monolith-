<?php

namespace Modules\Library\Controllers;

use App\Http\Controllers\Controller;
use Modules\Library\Exceptions\AuthorHasBooksException;
use Modules\Library\Models\Author;
use Modules\Library\Requests\StoreAuthorRequest;
use Modules\Library\Requests\UpdateAuthorRequest;

class AuthorController extends Controller
{
    public function index()
    {
        $authors = Author::withCount('books')->latest()->paginate(10);

        return view('authors.index', compact('authors'));
    }

    public function create()
    {
        return view('authors.create');
    }

    public function store(StoreAuthorRequest $request)
    {
        Author::create($request->validated());

        return redirect()
            ->route('authors.index')
            ->with('success', 'Penulis berhasil ditambahkan.');
    }

    public function edit(Author $author)
    {
        return view('authors.edit', compact('author'));
    }

    public function update(UpdateAuthorRequest $request, Author $author)
    {
        $author->update($request->validated());

        return redirect()
            ->route('authors.index')
            ->with('success', 'Penulis berhasil diperbarui.');
    }

    public function destroy(Author $author)
    {
        $bookCount = $author->books()->count();

        if ($bookCount > 0) {
            throw new AuthorHasBooksException($author->name, $bookCount);
        }

        $author->delete();

        return redirect()
            ->route('authors.index')
            ->with('success', 'Penulis berhasil dihapus.');
    }
}
