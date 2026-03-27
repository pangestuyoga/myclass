<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/share/presensi/{token}', [\App\Http\Controllers\ShareAttendanceController::class, 'show'])->name('share.attendance');
