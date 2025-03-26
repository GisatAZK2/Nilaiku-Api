<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\PredictionController;

Route::post('/', [PredictionController::class, 'createAcademicRecord']);
Route::get('/all', [PredictionController::class, 'generateRecommendation']);