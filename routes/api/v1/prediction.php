<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\PredictionController;

Route::post('/', [PredictionController::class, 'predict']);
Route::get('/academic-record/{student_id}', [PredictionController::class, 'showAcademicRecordByStudentId']);