<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\StudentController;

Route::prefix('v1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']); 
    Route::post('/login', [AuthController::class, 'login']); 
    Route::prefix('subjects')->group(base_path('routes/api/v1/subjects.php'));
    Route::prefix('predict')->group(base_path('routes/api/v1/prediction.php'));
});

Route::post('register', [StudentController::class, 'register']);
Route::get('/students', [StudentController::class, 'publicStudents']); // Bisa diakses tanpa login
Route::middleware('auth:sanctum')->get('/students/private', [StudentController::class, 'index']);


