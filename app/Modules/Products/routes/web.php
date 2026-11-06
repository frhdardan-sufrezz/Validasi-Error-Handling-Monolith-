<?php

use Illuminate\Support\Facades\Route;
use Modules\Products\Controllers\ProductController;

// Wajib menggunakan middleware 'web' agar session & auth terbaca
Route::middleware(['web', 'auth'])->group(function () {
    Route::post('products/{product}/reduce-stock', [ProductController::class, 'reduceStock'])
        ->name('products.reduce-stock');

    Route::resource('products', ProductController::class);
});
