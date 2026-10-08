<?php

use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::get('/dashboard', function () {
    return Inertia\Inertia::render('Dashboard', [
        'achievements' => request()->user()->achievements,
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admindashboard', [DashboardController::class, 'index'])->name('admindashboard');
    Route::post('/admin/users/{user}/achievements', [DashboardController::class, 'storeAchievement'])->name('admin.achievements.store');
});

require __DIR__.'/settings.php';
