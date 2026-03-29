<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('filament.admin.auth.login');
});

Route::get('/share/presensi/{token}', [\App\Http\Controllers\ShareAttendanceController::class, 'show'])->name('share.attendance');
