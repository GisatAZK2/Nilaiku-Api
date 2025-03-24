<?php

use Illuminate\Support\Facades\Http;

function predictStudent(Request $request)
{
    $response = Http::post('http://127.0.0.1:5000/predict', $request->all());

    return response()->json($response->json());
}
