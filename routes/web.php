<?php

use App\Http\Controllers\ShareAttendanceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/share/presensi/{token}', [ShareAttendanceController::class, 'show'])->name('share.attendance');
