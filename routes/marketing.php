<?php

use App\Billing\Plan;
use App\Http\Controllers\HelpController;
use App\Support\Demo;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

/*
 * The marketing pages outside the app: the home page and the help center.
 * They run in the `marketing` middleware group (bootstrap/app.php): no
 * session, no cookies, no CSRF token and nothing about the visitor, so
 * Laravel Cloud's edge can cache them (App\Http\Middleware\ServeFromEdgeCache).
 * The browser always loads them as whole pages
 * (resources/js/lib/edge-cached-pages.ts; keep the paths in step).
 */

Route::get('/', fn () => Inertia::render('welcome', [
    'demo' => Demo::enabled(),
    'canRegister' => Features::enabled(Features::registration()),
    'pricing' => Plan::catalog(),
]))->name('home');

// The public help center. Articles are Markdown in resources/help.
Route::controller(HelpController::class)->prefix('help')->name('help.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('search', 'search')->name('search');
    Route::get('suggest', 'suggest')->middleware('throttle:120,1')->name('suggest');
    Route::get('{category}', 'category')->where('category', '[a-z0-9-]+')->name('category');
    Route::get('{category}/{article}', 'article')->where(['category' => '[a-z0-9-]+', 'article' => '[a-z0-9-]+'])->name('article');
});
