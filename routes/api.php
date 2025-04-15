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

// Route::options('/{any}', function () {
//     return response()->json(['message' => 'OK'])
//         ->header('Access-Control-Allow-Origin', ['http://localhost:3000', 'http://127.0.0.1:3000', 'http://localhost:5173', 'http://127.0.0.1:5173', 'http://localhost:8000', 'http://127.0.0.1:8000', 'https://nilaiku.vercel.app', 'https://nilaiku-api.up.railway.app'])
//         ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
//         ->header('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization, X-Requested-With');
// })->where('any', '.*');
