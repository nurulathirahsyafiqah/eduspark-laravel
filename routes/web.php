<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PerformanceController;

Route::get('/', function () {
    return view('welcome');
});

// ✅ Add this (do not replace anything)
Route::get('/performance', [PerformanceController::class, 'index']);
