<?php
namespace App\Http\Controllers\Api\V1;

use App\Helpers\PredictionHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\PredictionRequest;
use App\Models\User;
use App\Services\GuestSessionService;
use App\Services\PredictionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class PredictionController extends Controller
{
    protected $predictionService;
    protected $guestSessionService;

    public function __construct(
        PredictionService $predictionService,
        GuestSessionService $guestSessionService
    ) {
        $this->predictionService   = $predictionService;
        $this->guestSessionService = $guestSessionService;
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
     *              required={"subject_id", "attendance", "hours_studied", "previous_scores", "sleep_hours", "tutoring_sessions", "peer_influence", "motivation_level", "teacher_quality", "access_to_resources"},
     *              @OA\Property(property="student_id", type="number", example=1),
     *              @OA\Property(property="subject_id", type="number", example=1),
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
     *          description="Predicted successfully",
     *          @OA\JsonContent(
     *          @OA\Property(property="message", type="string", example="Predicted successfully"),
     *      @OA\Property(property="student", type="object",
     *          @OA\Property(property="id", type="integer", example=1),
     *          @OA\Property(property="name", type="string", example="John Doe"),
     *          @OA\Property(property="age", type="integer", example=20),
     *          @OA\Property(property="gender", type="string", example="male"),
     *          @OA\Property(property="education", type="string", example="SMA"),
     *          @OA\Property(property="user_id", type="integer", example=null, nullable=true),
     *          @OA\Property(property="guest_session_token", type="string", example=eb68fc6f-173f-4cbe-a710-780c1cfc21cd, nullable=true),
     *          @OA\Property(property="created_at", type="string", format="date-time"),
     *          @OA\Property(property="updated_at", type="string", format="date-time")
     *      ),
     *      @OA\Property(property="record", type="object",
     *          @OA\Property(property="subject_id", type="integer", example=1),
     *          @OA\Property(property="input_date", type="string", format="date-time", example="2025-04-11T08:31:54.812441Z"),
     *          @OA\Property(property="attendance", type="integer", example=0),
     *          @OA\Property(property="hours_studied", type="float", example=0),
     *          @OA\Property(property="previous_scores", type="float", example=0),
     *          @OA\Property(property="sleep_hours", type="float", example=0),
     *          @OA\Property(property="tutoring_sessions", type="integer", example=0),
     *          @OA\Property(property="peer_influence", type="string", example="negative"),
     *          @OA\Property(property="motivation_level", type="string", example="low"),
     *          @OA\Property(property="teacher_quality", type="string", example="low"),
     *          @OA\Property(property="access_to_resources", type="string", example="low")
     *      ),
     *      @OA\Property(property="prediction_result", type="object",
     *          @OA\Property(property="prediction_date", type="string", format="date-time", example="2025-04-11T08:31:54.812490Z"),
     *          @OA\Property(property="predicted_score", type="number", format="float", example=85.58453369140625),
     *          @OA\Property(property="recommendation", type="string", example="Excellent performance")
     *      )
     *  )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="The given data was invalid"),
     *              @OA\Property(property="errors", type="object",
     *                  @OA\Property(property="attendance", type="array",
     *                      @OA\Items(type="string", example="Attendance must be between 0 and 100.")
     *                  ),
     *                  @OA\Property(property="hours_studied", type="array",
     *                      @OA\Items(type="string", example="Hours studied must be a number.")
     *                  )
     *              )
     *          )
     *      )
     * )
     */
    public function predict(PredictionRequest $request)
    {
        $validatedData  = $request->validated();
        $dataPrediction = PredictionHelper::convertToModelFormat($validatedData);

        //Post to Model Flask API
        $responseModel = $this->predictionService->getPrediction($dataPrediction);

        if (isset($responseModel['error'])) {
            return response()->json(['error' => $responseModel['error']], 500);
        }

        // Predict Result
        $predictedScore = $responseModel['predicted_score'];

        $predictionResult = [
            'prediction_date' => now(),
            'predicted_score' => $predictedScore,
            'recommendation'  => $this->generateRecommendation($predictedScore),
        ];

        // Cek auth user
        $userIdOrGuestToken = Auth::check() ? Auth::id() : Session::get('guest_session_id');

        if (! Auth::check()) {
            $responseGuest = $this->guestSessionService->storeGuestPrediction($validatedData, $predictionResult);
        }
        $response = $this->predictionService->storePrediction($validatedData, $predictionResult, $userIdOrGuestToken);

        return response()->json([
            'message'           => 'Predicted successfully',
            'student'           => $response['student'] ?? null,
            'record'            => $response['record'],
            'prediction_result' => $response['prediction_result'],
        ], 201);
    }

    private function generateRecommendation($score = null)
    {
        if ($score < 40) {
            return 'Requires significant improvement';
        }

        if ($score < 75) {
            return 'Needs additional support';
        }

        if ($score < 85) {
            return 'Good progress, keep working';
        }

        return 'Excellent performance';
    }
}
