
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\StudentController;

Route::post('/', [StudentController::class, 'store']);
Route::post('/public-student', [StudentController::class, 'publicStudents']);
Route::get('/{id}', [StudentController::class, 'showStudentForGuest']);
Route::put('/{id}', [StudentController::class, 'updateStudent']);
