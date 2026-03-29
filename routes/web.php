<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontController;
use Marble\Admin\Facades\Marble;

// Test route: load any item by ID (no locale)
Route::get('/marble-test/{id}', [FrontController::class, 'test'])->where('id', '[0-9]+');

// Catch-all: resolve any path to a Marble item and render its view
Marble::routes(function (\Marble\Admin\Models\Item $item) {
    return view(Marble::viewFor($item), ['item' => $item]);
});
