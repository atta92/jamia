<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\HadithController;
use App\Http\Controllers\HadithErrorReportController;
use App\Http\Controllers\HifzController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PrayerTimesController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [PageController::class, 'storeContact'])->name('contact.store');
Route::get('/hifz', HifzController::class)->name('hifz.show');
Route::get('/api/prayer-times', PrayerTimesController::class)->name('api.prayer-times');

Route::get('/gallery/photos', [GalleryController::class, 'photos'])->name('gallery.photos');
Route::get('/gallery/videos', [GalleryController::class, 'videos'])->name('gallery.videos');

Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{slug}', [EventController::class, 'show'])->name('events.show');

Route::get('/hadith', [HadithController::class, 'index'])->name('hadith.index');
Route::get('/hadith/{book}', [HadithController::class, 'book'])->name('hadith.book');
Route::get('/hadith/{book}/{chapter}', [HadithController::class, 'chapter'])
    ->whereNumber('chapter')
    ->name('hadith.chapter');
Route::post('/hadith/error-reports', [HadithErrorReportController::class, 'store'])
    ->middleware('throttle:8,1')
    ->name('hadith.error-reports.store');
