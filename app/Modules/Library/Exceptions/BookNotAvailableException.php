<?php

namespace Modules\Library\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BookNotAvailableException extends Exception
{
    public function __construct(
        protected string $bookTitle,
        protected int $bookId,
    ) {
        parent::__construct(
            "Buku '{$bookTitle}' sedang tidak tersedia (stok 0) sehingga tidak dapat dipinjam."
        );
    }

    public function report(): void
    {
        Log::warning('BookNotAvailableException: '.$this->getMessage(), [
            'book_id' => $this->bookId,
            'book_title' => $this->bookTitle,
        ]);
    }

    public function render(Request $request): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $this->getMessage(),
                'error' => 'book_not_available',
                'details' => [
                    'book_id' => $this->bookId,
                    'book_title' => $this->bookTitle,
                ],
            ], 422);
        }

        return back()->with('error', $this->getMessage());
    }
}
