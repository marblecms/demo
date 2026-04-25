<?php

use Illuminate\Support\Facades\Route;
use MarbleCms\Newsletter\Http\Controllers\Admin\NewsletterController;
use MarbleCms\Newsletter\Http\Controllers\Admin\SubscriberController;
use MarbleCms\Newsletter\Http\Controllers\Admin\ListController;
use MarbleCms\Newsletter\Http\Controllers\Admin\CampaignController;

Route::prefix('newsletter')->group(function () {
    Route::get('', [NewsletterController::class, 'index'])->name('newsletter.index');

    // Subscribers
    Route::get('subscribers',          [SubscriberController::class, 'index'])->name('newsletter.subscribers.index');
    Route::delete('subscribers/{subscriber}', [SubscriberController::class, 'destroy'])->name('newsletter.subscribers.destroy');

    // Lists
    Route::get('lists',          [ListController::class, 'index'])->name('newsletter.lists.index');
    Route::post('lists',         [ListController::class, 'store'])->name('newsletter.lists.store');
    Route::delete('lists/{list}', [ListController::class, 'destroy'])->name('newsletter.lists.destroy');

    // Campaigns
    Route::get('campaigns',                   [CampaignController::class, 'index'])->name('newsletter.campaigns.index');
    Route::get('campaigns/create',            [CampaignController::class, 'create'])->name('newsletter.campaigns.create');
    Route::post('campaigns',                  [CampaignController::class, 'store'])->name('newsletter.campaigns.store');
    Route::get('campaigns/{campaign}',        [CampaignController::class, 'show'])->name('newsletter.campaigns.show');
    Route::patch('campaigns/{campaign}',      [CampaignController::class, 'update'])->name('newsletter.campaigns.update');
    Route::delete('campaigns/{campaign}',     [CampaignController::class, 'destroy'])->name('newsletter.campaigns.destroy');
    Route::post('campaigns/{campaign}/send',  [CampaignController::class, 'send'])->name('newsletter.campaigns.send');
});
