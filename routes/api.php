<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;

Route::post('register', [StudentController::class, 'register']);
Route::get('/students', [StudentController::class, 'publicStudents']); // Bisa diakses tanpa login
Route::middleware('auth:sanctum')->get('/students/private', [StudentController::class, 'index']);
Route::post('/register', [StudentController::class, 'register']);


