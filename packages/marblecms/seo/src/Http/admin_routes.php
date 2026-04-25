<?php

use Illuminate\Support\Facades\Route;
use MarbleCms\Seo\Http\Controllers\Admin\SeoController;

Route::prefix('seo')->group(function () {
    Route::get('',              [SeoController::class, 'index'])->name('seo.index');
    Route::get('item/{item}',   [SeoController::class, 'edit'])->name('seo.edit');
    Route::post('item/{item}',  [SeoController::class, 'update'])->name('seo.update');
});
