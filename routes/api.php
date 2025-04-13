<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\StudentController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\ReportController;

Route::prefix('v1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::prefix('subjects')->group(base_path('routes/api/v1/subjects.php'));
    Route::prefix('students')->group(base_path('routes/api/v1/students.php'));
    Route::prefix('predict')->group(base_path('routes/api/v1/prediction.php'));

    Route::post('/guest-session', [StudentController::class, 'createGuestSession']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

Route::post('register', [StudentController::class, 'register']);
Route::post('/feedbacks', [FeedbackController::class, 'store']);
Route::post('/report', [ReportController::class, 'store']);
Route::get('/students', [StudentController::class, 'publicStudents']); // Bisa diakses tanpa login
Route::middleware('auth:sanctum')->get('/students/private', [StudentController::class, 'index']);



