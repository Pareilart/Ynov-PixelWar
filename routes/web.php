<?php

use App\Http\Controllers\PixelController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('pixels')->group(function () {
        Route::get('/', [PixelController::class, 'index'])->name('pixels.index');
        Route::post('/', [PixelController::class, 'store'])->name('pixels.store');
        Route::get('/{pixel}', [PixelController::class, 'show'])->name('pixels.show');
        Route::put('/{pixel}', [PixelController::class, 'update'])->name('pixels.update');
        Route::delete('/{pixel}', [PixelController::class, 'destroy'])->name('pixels.destroy');
    });
});

require __DIR__.'/auth.php';
