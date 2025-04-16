<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\StudentController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

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

Route::post('/feedbacks', [FeedbackController::class, 'store']);
Route::post('/report', [ReportController::class, 'store']);
Route::middleware('auth:sanctum')->get('/students/private', [StudentController::class, 'index']);

Route::options('{any}', function () {
    return response()->noContent();
})->where('any', '.*');
