<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Lesson;
use App\Http\Controllers\PerformanceController;
use App\Http\Controllers\Api\LessonController;

// Default route (optional — choose one)
Route::get('/', function () {
    return redirect('/lessons-table'); // redirect to lessons table instead of welcome
});

// Performance page route
Route::get('/performance', [PerformanceController::class, 'index']);

// Lessons table (Blade view)
Route::get('/lessons-table', function () {
    return view('lessons');
});

// API routes for LessonController
Route::prefix('api')->group(function () {
    Route::get('/lessons', [LessonController::class, 'index']);
    Route::post('/lessons', [LessonController::class, 'store']);
    Route::post('/lessons/{id}', [LessonController::class, 'update']); // POST with _method=PUT
    Route::post('/lessons/{id}/delete', [LessonController::class, 'destroy']);
});

// Extra fallback if needed
Route::fallback(function () {
    return redirect('/');
});
