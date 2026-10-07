<?php
use Illuminate\Support\Facades\Route;
use Modules\Account\Http\Controllers\ProfileController;
//I overestimated the power of starter kit. It already has modules. XDXD
Route::middleware('auth')->group(function () {
//    Route::put('/my-profile', [ProfileController::class, 'update'])->name('profile.update');
});
