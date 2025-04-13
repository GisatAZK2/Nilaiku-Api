
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\StudentController;

Route::post('/', [StudentController::class, 'store']);
Route::get('/public-student', [StudentController::class, 'publicStudents']);
Route::get('/student-detail', [StudentController::class, 'showStudentForGuest']);