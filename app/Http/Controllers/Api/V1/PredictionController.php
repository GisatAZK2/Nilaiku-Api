<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\AcademicRecord;
use App\Models\PredictionResult;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class PredictionController extends Controller
{
    public function createAcademicRecord(Request $request)
    {
        $validatedData = $request->validate([
            // 'student_id' => 'required|exists:students,student_id',
            // 'session_token' => 'required',
            'attendance' => 'required|numeric|between:0,100',
            'hours_studied' => 'required|numeric',
            'previous_scores' => 'required|numeric',
            'tutoring_sessions' => 'required|integer',
            'physical_activity' => 'required|numeric',
            'sleep_hours' => 'required|numeric',
            'parental_involvement' => 'required|in:low,medium,high',
            'access_to_resources' => 'required|in:low,medium,high'
        ]);

        // $student = Student::where('student_id', $validatedData['student_id'])
        //     ->where('session_token', $validatedData['session_token'])
        //     ->firstOrFail();

        // $academicRecord = AcademicRecord::create([
        //     'student_id' => $student->student_id ?? null,
        //     'input_date' => $validatedData['input_date'],
        //     'attendance' => $validatedData['attendance'],
        //     'hours_studied' => $validatedData['hours_studied'],
        //     'previous_scores' => $validatedData['previous_scores'],
        //     'tutoring_sessions' => $validatedData['tutoring_sessions'],
        //     'physical_activity' => $validatedData['physical_activity'],
        //     'sleep_hours' => $validatedData['sleep_hours'],
        //     'parental_involvement' => $validatedData['parental_involvement'],
        //     'access_to_resources' => $validatedData['access_to_resources']
        // ]);

        $response = Http::post('http://127.0.0.1:5001/predict', [
            'attendance' => $validatedData['attendance'],
            'hours_studied' => $validatedData['hours_studied'],
            'previous_scores' => $validatedData['previous_scores'],
            'tutoring_sessions' => $validatedData['tutoring_sessions'],
            'physical_activity' => $validatedData['physical_activity'],
            'sleep_hours' => $validatedData['sleep_hours'],
            'parental_involvement' => $validatedData['parental_involvement'] === 'low' ? 1 : ($validatedData['parental_involvement'] === 'medium' ? 2 : 3),
            'access_to_resources' => $validatedData['access_to_resources'] === 'low' ? 1 : ($validatedData['access_to_resources'] === 'medium' ? 2 : 3)
        ])->json();
        
        if (isset($response['error'])) {
            return response()->json(['error' => $response['error']], 500);
        }

        $predictedScore = $response->json()['predicted_score'];

        $predictionResult = PredictionResult::create([
            'record_id' => 1,
            'prediction_date' => now(),
            'predicted_score' => $predictedScore,
            'recommendation' => $this->generateRecommendation($predictedScore)
        ]);

        return response()->json([
            'academic_record_id' => $validatedData['record_id'],
            'prediction_result' => $predictionResult
        ], 201);
    }

    private function generateRecommendation($score = 50)
    {
        if ($score < 40) return 'Requires significant improvement';
        if ($score < 60) return 'Needs additional support';
        if ($score < 80) return 'Good progress, keep working';
        return 'Excellent performance';
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
