<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/predict', function () {
    $data = [
        'Hours_Studied' => 25,
        'Attendance' => 90,
        'Parental_Involvement' => 'Medium',
        'Access_to_Resources' => 'High',
        'Extracurricular_Activities' => 'Yes',
        'Sleep_Hours' => 7,
        'Previous_Scores' => 85,
        'Motivation_Level' => 'High',
        'Internet_Access' => 'Yes'
    ];
    
    dd($data); // Debug sebelum request ke Flask
    

    $response = Http::post('http://127.0.0.1:5000/predict', $data);

    return response()->json($response->json());
});

