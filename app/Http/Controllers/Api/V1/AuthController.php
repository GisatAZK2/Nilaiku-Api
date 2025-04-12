<?php
namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\AcademicRecord;
use App\Models\PredictionResult;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *      path="/api/v1/register",
     *      tags={"Auth"},
     *      summary="Registrasi pengguna baru",
     *      description="Mendaftarkan pengguna baru dengan validasi email, nama, dan password.",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name", "email", "password"},
     *              @OA\Property(property="name", type="string", example="John Doe"),
     *              @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *              @OA\Property(property="password", type="string", format="password", example="Password@22"),
     *              @OA\Property(property="password_confirmation", type="string", format="password", example="Password@22")
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="User registered successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="User registered successfully"),
     *              @OA\Property(property="user", type="object",
     *                  @OA\Property(property="name", type="string", example="John Doe"),
     *                  @OA\Property(property="email", type="string", example="john@example.com"),
     *                  @OA\Property(property="updated_at", type="date-time", example="2025-04-11T08:17:24.000000Z"),
     *                  @OA\Property(property="created_at", type="date-time", example="2025-04-11T08:17:24.000000Z"),
     *                  @OA\Property(property="id", type="integer", example=1)
     *              ),
     *              @OA\Property(property="access_token", type="string", example="1|Ym1ySoVCLX4vFgPoWW0KwoyzCgeEDnCdna2FcDd3a56e2061"),
     *              @OA\Property(property="token_type", type="string", example="Bearer")
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Validation error"),
     *              @OA\Property(property="errors", type="object",
     *                  @OA\Property(property="email", type="array",
     *                      @OA\Items(type="string", example="Email ini sudah terdaftar.")
     *                  )
     *              )
     *          )
     *      )
     * )
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|unique:users',
            'password' => 'required|confirmed|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/',
        ]);

        $errors = [];

        if (User::where('email', $request->email)->exists()) {
            return response()->json([
                'message' => 'Validation error',
                'errors'  => [
                    'email' => ['Email ini sudah terdaftar.'],
                ],
            ], 422);
        }

        if (! empty($errors)) {
            return response()->json([
                'message' => 'Validation error',
                'errors'  => $errors,
            ], 422);
        }

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => 'siswa',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        
        if (session()->has('guest_student_data')) {
            $student = Student::create(attributes: [
                'user_id' => $user->id,
                'name' => $user->name,
                'gender' => null,
                'date_of_birth' => null,
                'is_guest' => false,
            ]);
            // $guest = session('guest_prediction_data');
            // $guestToken = session('guest_prediction_data');
        
            // $student = Student::firstOrCreate(
            //     ['user_id' => $user->id], 
            //     [
            //         'user_id' => $user->id,
            //         'name' => $user->name,
            //         'gender' => $guest['student_data']['gender'],
            //         'date_of_birth' => $guest['student_data']['date_of_birth'],
            //         'is_guest' => false,
            //     ]
            // );
        
            // $record = AcademicRecord::firstOrCreate(
            //     ['student_id' => $student->id],
            //     [
            //         'student_id' => $student->id,
            //         'subject_id' => $guest['academic_record']['subject_id'],
            //         'input_date' => now(),
            //         'attendance' => $guest['academic_record']['attendance'],
            //         'hours_studied' => $guest['academic_record']['hours_studied'],
            //         'previous_scores' => $guest['academic_record']['previous_scores'],
            //         'tutoring_sessions' => $guest['academic_record']['tutoring_sessions'],
            //         'physical_activity' => $guest['academic_record']['physical_activity'],
            //         'sleep_hours' => $guest['academic_record']['sleep_hours'],
            //         'parental_involvement' => $guest['academic_record']['parental_involvement'],
            //         'access_to_resources' => $guest['academic_record']['access_to_resources'],
            //     ]
            // );
        
            // PredictionResult::create([
            //     'academic_record_id' => $record->id,
            //     'prediction_date' => now(),
            //     'predicted_score' => $guest['prediction_result']['predicted_score'],
            //     'recommendation' => $guest['prediction_result']['recommendation'],
            // ]);
        
            // session()->forget('guest_prediction');
            session()->forget('guest_session_id');
        }        

        return response()->json([
            'message' => 'User registered successfully',
            'user'    => Arr::except($user, ['role']),
            'access_token' => $token,
            'token_type' => 'Bearer'
        ], 201);
    }

    /**
     * @OA\Post(
     *      path="/api/v1/login",
     *      tags={"Auth"},
     *      summary="Login pengguna",
     *      description="Mengautentikasi pengguna dan mengembalikan token JWT.",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"email", "password"},
     *              @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *              @OA\Property(property="password", type="string", format="password", example="Password@22"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Login success",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Login success"),
     *              @OA\Property(property="access_token", type="string", example="1|Ym1ySoVCLX4vFgPoWW0KwoyzCgeEDnCdna2FcDd3a56e2061"),
     *              @OA\Property(property="token_type", type="string", example="Bearer")
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Unauthorized")
     *          )
     *      )
     * )
     */
    public function login(Request $request)
    {
        $credentials = request(['email', 'password']);

        if (! auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login success',
            'access_token' => $token,
            'token_type' => 'Bearer'
        ], 200);
    }

    public function logout()
    {
        Auth::user()->tokens()->delete();
        return response()->json([
            'message' => 'Logout success'
        ]);
    }
}
