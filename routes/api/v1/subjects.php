<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\SubjectController;

Route::get('/', [SubjectController::class, 'index']);
Route::post('/', [SubjectController::class, 'store']);
Route::middleware('auth:sanctum')->group(function () {
    Route::put('/{subject}', [SubjectController::class, 'update']);
    Route::delete('/{subject}', [SubjectController::class, 'destroy']);
});