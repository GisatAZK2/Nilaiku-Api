<?php
namespace App\Http\Controllers\Api\V1;

use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\AcademicRecord;
use App\Models\PredictionResult;
use App\Services\PredictionService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class PredictionController extends Controller
{
    protected $predictionService;

    public function __construct(PredictionService $predictionService)
    {
        $this->predictionService = $predictionService;
    }

    /**
     * @OA\Post(
     *      path="/api/v1/predict",
     *      tags={"Predict"},
     *      summary="Prediksi nilai akademik",
     *      description="Memprediksi nilai akademik berdasarkan data yang diinput pengguna.",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"attendance", "hours_studied", "previous_scores", "sleep_hours", "tutoring_sessions", "peer_influence", "motivation_level", "teacher_quality", "access_to_resources"},
     *              @OA\Property(property="attendance", type="number", example=90),
     *              @OA\Property(property="hours_studied", type="number", example=5),
     *              @OA\Property(property="previous_scores", type="number", example=75),
     *              @OA\Property(property="sleep_hours", type="number", example=8),
     *              @OA\Property(property="tutoring_sessions", type="integer", example=2),
     *              @OA\Property(property="peer_influence", type="string", enum={"positive", "neutral", "negative"}, example="positive"),
     *              @OA\Property(property="motivation_level", type="string", enum={"low", "medium", "high"}, example="high"),
     *              @OA\Property(property="teacher_quality", type="string", enum={"low", "medium", "high"}, example="medium"),
     *              @OA\Property(property="access_to_resources", type="string", enum={"low", "medium", "high"}, example="high")
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Prediksi nilai berhasil",
     *          @OA\JsonContent(
     *              @OA\Property(property="academic_record_id", type="integer", example=1),
     *              @OA\Property(property="prediction_result", type="object",
     *                  @OA\Property(property="record_id", type="integer", example=1),
     *                  @OA\Property(property="prediction_date", type="string", format="date-time", example="2023-10-01T12:00:00Z"),
     *                  @OA\Property(property="predicted_score", type="number", example=85),
     *                  @OA\Property(property="recommendation", type="string", example="Perbanyak belajar.")
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Kesalahan validasi",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Kesalahan validasi"),
     *              @OA\Property(property="errors", type="object",
     *                  @OA\Property(property="attendance", type="array",
     *                      @OA\Items(type="string", example="Attendance harus antara 0 dan 100.")
     *                  ),
     *                  @OA\Property(property="hours_studied", type="array",
     *                      @OA\Items(type="string", example="Hours studied harus berupa angka.")
     *                  )
     *              )
     *          )
     *      )
     * )
     */
    public function createAcademicRecord(Request $request)
    {
        $validatedData = $request->validate([
            // 'student_id' => 'nullable|exists:students,student_id',
            // 'guest_session_token' => 'nullable',
            // 'subject_id' => 'required',
            'attendance'          => 'required|numeric|between:0,100',
            'hours_studied'       => 'required|numeric',
            'previous_scores'     => 'required|numeric|between:0,100',
            'sleep_hours'         => 'required|numeric|between:0,24',
            'tutoring_sessions'   => 'required|integer',
            'peer_influence'      => 'required|in:positive,neutral,negative',
            'motivation_level'    => 'required|in:low,medium,high',
            'teacher_quality'     => 'required|in:low,medium,high',
            'access_to_resources' => 'required|in:low,medium,high',
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

        $data = [
            'attendance'          => $validatedData['attendance'],
            'hours_studied'       => $validatedData['hours_studied'],
            'previous_scores'     => $validatedData['previous_scores'],
            'sleep_hours'         => $validatedData['sleep_hours'],
            'tutoring_sessions'   => $validatedData['tutoring_sessions'],
            'peer_influence'      => $validatedData['peer_influence'] === 'positive' ? 1 : ($validatedData['peer_influence'] === 'neutral' ? 2 : 3),
            'motivation_level'    => $validatedData['motivation_level'] === 'low' ? 1 : ($validatedData['motivation_level'] === 'medium' ? 2 : 3),
            'teacher_quality'     => $validatedData['teacher_quality'] === 'low' ? 1 : ($validatedData['teacher_quality'] === 'medium' ? 2 : 3),
            'access_to_resources' => $validatedData['access_to_resources'] === 'low' ? 1 : ($validatedData['access_to_resources'] === 'medium' ? 2 : 3),
        ];
        //Post to Model Flask API
        // $mlApiUrl = config('services.ml_prediction.url');
        // $response = Http::post($mlApiUrl, $data)->json();
        $response = $this->predictionService->getPrediction($data);

        if (isset($response['error'])) {
            return response()->json(['error' => $response['error']], 500);
        }

        // Predict Result
        $predictedScore = $response['predicted_score'];

        $predictionResult = [
            'record_id'       => 1,
            'prediction_date' => now(),
            'predicted_score' => $predictedScore,
            'recommendation'  => $this->generateRecommendation($predictedScore),
        ];

        return response()->json([
            // 'academic_record_id' => $validatedData['record_id'],
            'prediction_result'  => $predictionResult,
        ], 201);
    }

    private function generateRecommendation($score = null)
    {
        if ($score < 40) {
            return 'Requires significant improvement';
        }

        if ($score < 60) {
            return 'Needs additional support';
        }

        if ($score < 80) {
            return 'Good progress, keep working';
        }

        return 'Excellent performance';
    }
}
