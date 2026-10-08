<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Inertia\Inertia;
Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', function () {
        $counters = DB::table('counters')->get()->keyBy('key');

        return Inertia::render('Dashboard', [
            'noFeedingDays' => (int) Carbon::parse($counters['no_feeding']->reset_at ?? now())->diffInDays(now()),
            'noIncidentsDays' => (int) Carbon::parse($counters['no_incidents']->reset_at ?? now())->diffInDays(now()),
        ]);
    })->name('dashboard');

    Route::post('dashboard/reset-counter/{key}', function (string $key) {
        DB::table('counters')->where('key', $key)->update([
            'reset_at' => now(),
            'updated_at' => now(),
        ]);

        return back();
    })->name('counters.reset');
});

require __DIR__.'/settings.php';
