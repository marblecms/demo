<?php

use Illuminate\Support\Facades\Route;
use MarbleCms\Seo\Http\Controllers\SitemapController;
use MarbleCms\Seo\Http\Controllers\RobotsController;

Route::get('/sitemap.xml', SitemapController::class)->name('seo.sitemap');
Route::get('/robots.txt',  RobotsController::class)->name('seo.robots');
