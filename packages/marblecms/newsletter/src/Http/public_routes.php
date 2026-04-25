<?php

use Illuminate\Support\Facades\Route;
use MarbleCms\Newsletter\Http\Controllers\Public\SubscribeController;
use MarbleCms\Newsletter\Http\Controllers\Public\TrackingController;

Route::prefix('newsletter')->group(function () {
    Route::post('subscribe',                    [SubscribeController::class, 'subscribe'])->name('newsletter.subscribe');
    Route::get('confirm/{token}',               [SubscribeController::class, 'confirm'])->name('newsletter.confirm');
    Route::get('unsubscribe/{token}',           [SubscribeController::class, 'showUnsubscribe'])->name('newsletter.unsubscribe.show');
    Route::post('unsubscribe/{token}',          [SubscribeController::class, 'unsubscribe'])->name('newsletter.unsubscribe');

    // Tracking
    Route::get('track/open/{token}',            [TrackingController::class, 'open'])->name('newsletter.track.open');
    Route::get('track/click/{token}/{url}',     [TrackingController::class, 'click'])->name('newsletter.track.click');
});
