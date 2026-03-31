<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ShareAssignmentController;
use App\Http\Controllers\ShareAttendanceController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index']);

Route::get('/share/attendance/{course}', [ShareAttendanceController::class, 'show'])->name('share.attendance')->middleware('signed');
Route::get('/share/assignment/{course}', [ShareAssignmentController::class, 'show'])->name('share.assignment')->middleware('signed');
