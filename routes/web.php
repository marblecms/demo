<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\SearchController;
use Marble\Admin\Facades\Marble;

// Test route: load any item by ID (no locale)
Route::get('/marble-test/{id}', [FrontController::class, 'test'])->where('id', '[0-9]+');

// Search route — must be before the Marble catch-all
Route::get('/search', [SearchController::class, 'index'])->name('search');

// Catch-all: resolve any path to a Marble item and render its view
Marble::routes(function (\Marble\Admin\Models\Item $item) {
    return view(Marble::viewFor($item), ['item' => $item]);
});
