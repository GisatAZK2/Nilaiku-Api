<?php
namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Models\Student;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StudentRequest;
use Illuminate\Support\Facades\Session;
use Illuminate\Routing\Controllers\Middleware;
use App\Services\StudentService;

class StudentController extends Controller
{
    protected $studentService;

    public function __construct(StudentService $studentService) {
        $this->studentService = $studentService;
    }

    /**
     * Atur middleware di Laravel 12
     */
    public static function boot(): array
    {
        return [
            new Middleware('auth:sanctum', except: ['publicStudents']),
        ];
    }

    /**
     * Membuat sesi guest dengan token unik
     */
    public function createGuestSession(Request $request)
    {
        if (session()->has('guest_session_id')) {
            $student = Student::where('guest_session_token', session('guest_session_token'))->first();
        } else {
            $guestToken = Str::random(32);
            $student = Student::create([
                'guest_session_token' => $guestToken,
                'is_guest' => true
            ]);
            session(['guest_session_token' => $guestToken]);
        }

        return response()->json($student, 201);
    }

    /**
     * Ambil semua data siswa yang terkait dengan user yang login
     */
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $students = Student::where('user_id', $userId)->get();

        return response()->json($students);
    }

    /**
     * Endpoint untuk mendapatkan semua siswa (tanpa autentikasi)
     */
    public function publicStudents()
    {
        $students = Student::all();

        return response()->json($students);
    }

    /**
     * @OA\Post(
     *      path="/api/v1/students",
     *      tags={"Students"},
     *      summary="Tambah student baru",
     *      description="Menambahkan data student baru ke dalam sistem",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name", "age", "gender", "education"},
     *              @OA\Property(property="name", type="string", example="John Doe"),
     *              @OA\Property(property="age", type="integer", example=20),
     *              @OA\Property(property="gender", type="string", enum={"male", "female"}, example="male"),
     *              @OA\Property(property="education", type="string", example="SMA"),
     *              @OA\Property(property="user_id", type="integer", example=1, nullable=true)
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successfully retrieved data",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Student added successfully"),
     *              @OA\Property(property="student", type="object",
     *                  @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="name", type="string", example="John Doe"),
     *                  @OA\Property(property="age", type="integer", example=20),
     *                  @OA\Property(property="gender", type="string", example="male"),
     *                  @OA\Property(property="education", type="string", example="SMA"),
     *                  @OA\Property(property="user_id", type="integer", example=1, nullable=true),
     *                  @OA\Property(property="created_at", type="string", format="date-time"),
     *                  @OA\Property(property="updated_at", type="string", format="date-time")
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="The given data was invalid"),
     *              @OA\Property(property="errors", type="object",
     *                  @OA\Property(property="name", type="array",
     *                      @OA\Items(type="string", example="The name field is required.")
     *                  ),
     *                  @OA\Property(property="age", type="array",
     *                      @OA\Items(type="string", example="The age must be an integer.")
     *                  )
     *              )
     *          )
     *      )
     * )
     */
    public function store(StudentRequest $request)
    {
        $validated = $request->validated();

        $student = $this->studentService->createOrUpdateStudent($validated);

        if (! $student) {
            return response()->json([
                'message' => 'Error'
            ], 401);
        }

        return response()->json([
            'message' => 'Student added successfully',
            'student'    => $student,
        ], 201);
    }

    /**
     * @OA\Get(
     *      path="/api/v1/students/{id}",
     *      tags={"Students"},
     *      summary="Data detail student yang melakukan prediksi",
     *      description="Data detail student sesuai dengan pengguna yang membuka website .",
     *      @OA\Parameter(
     *          in="path",
     *          name="id",
     *          required=true,
     *          description="ID student",
     *          @OA\Schema(
     *              type="integer",
     *              format="int64",
     *              example=1
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successfully retrieved data",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Successfully retrieved data"),
     *              @OA\Property(property="student", type="object",
     *                  @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="name", type="string", example="John Doe"),
     *                  @OA\Property(property="age", type="integer", example=20),
     *                  @OA\Property(property="gender", type="string", example="male"),
     *                  @OA\Property(property="education", type="string", example="SMA"),
     *                  @OA\Property(property="user_id", type="integer", example=1, nullable=true),
     *                  @OA\Property(property="guest_session_token", type="string", example="eb68fc6f-173f-4cbe-a710-780c1cfc21cd", nullable=true),
     *                  @OA\Property(property="created_at", type="string", format="date-time"),
     *                  @OA\Property(property="updated_at", type="string", format="date-time")
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Data not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Data not found")
     *          )
     *      )
     * )
     */
    public function showStudentForGuest($id)

    {
        $student = $this->studentService->getStudentData($id);

        if (! $student) {
            return response()->json([
                'message' => 'Data not found'
            ], status: 404);
        }

        return response()->json([
            'message' => 'Successfully retrieved student',
            'student'    => $student,
        ], status: 200);
    }
<<<<<<< HEAD

    /**
      * @OA\Put(
      *     path="/api/v1/students/{id}",
      *     tags={"Students"},
      *     summary="Update data student (guest atau user)",
      *     description="Mengubah data student berdasarkan guest session atau user yang login.",
      *     @OA\Parameter(
      *         name="id",
      *         in="path",
      *         required=true,
      *         description="ID dari student yang ingin diupdate",
      *         @OA\Schema(type="integer")
      *     ),
      *     @OA\RequestBody(
      *         required=true,
      *         @OA\JsonContent(
      *             required={"name", "age", "gender", "education"},
      *             @OA\Property(property="name", type="string", example="Jane Doe"),
      *             @OA\Property(property="age", type="integer", example=21),
      *             @OA\Property(property="gender", type="string", enum={"male", "female"}, example="female"),
      *             @OA\Property(property="education", type="string", example="SMK Cibitung 1")
      *         )
      *     ),
      *     @OA\Response(
      *         response=200,
      *         description="Student updated successfully",
      *         @OA\JsonContent(
      *             @OA\Property(property="message", type="string", example="Student updated successfully"),
      *             @OA\Property(property="student", type="object")
      *         )
      *     ),
      *     @OA\Response(
      *         response=404,
      *         description="Student not found",
      *         @OA\JsonContent(
      *             @OA\Property(property="message", type="string", example="Student not found")
      *         )
      *     )
      * )
     */
    public function updateStudent(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'age' => 'required|integer',
            'gender' => 'required|in:male,female',
            'education' => 'required|string|max:255',
        ]);

        $student = Student::find($id);

        if (! $student) {
            return response()->json(['message' => 'Student not found'], 404);
        }

        $student->update($validated);

        return response()->json([
            'message' => 'Student updated successfully',
            'student' => $student,
        ], 200);
    }
=======
>>>>>>> parent of f1798b2 (Merge branch 'main' of https://github.com/RidyCh/api.nilaiku)
}
