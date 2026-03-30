<?php

use App\Http\Controllers\ShareAssignmentController;
use App\Http\Controllers\ShareAttendanceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/share/presensi/{course}', [ShareAttendanceController::class, 'show'])->name('share.attendance');
Route::get('/share/tugas/{course}', [ShareAssignmentController::class, 'show'])->name('share.assignment');
