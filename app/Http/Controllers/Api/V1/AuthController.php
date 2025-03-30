<?php
namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

/**
 * @OA\Info(title="Auth API", version="1.0")
 */

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
     *              @OA\Property(property="password", type="string", format="password", example="password123"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="User registered successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="User registered successfully"),
     *              @OA\Property(property="user", type="object",
     *                  @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="name", type="string", example="John Doe"),
     *                  @OA\Property(property="email", type="string", example="john@example.com"),
     *                  @OA\Property(property="role", type="string", example="siswa")
     *              )
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
     *                  ),
     *                  @OA\Property(property="password", type="array",
     *                      @OA\Items(type="string", example="Password sudah pernah digunakan.")
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
            'password' => 'required|min:6',
        ]);

        $errors = [];

        if (User::where('email', $request->email)->exists()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => [
                    'email' => ['Email ini sudah terdaftar.']
                ]
            ], 422);
        }

    
        $existingPasswords = User::pluck('password');
        foreach ($existingPasswords as $hashedPassword) {
            if (Hash::check($request->password, $hashedPassword)) {
                $errors['password'] = ['Password sudah pernah digunakan. Gunakan password lain.'];
                break;
            }
        }

        if (!empty($errors)) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $errors
            ], 422);
        }

        $user = User::create([
            'name'    => $data['name'],
            'email'   => $data['email'],
            'password' => Hash::make($data['password']),
            'role'    => 'siswa'
        ]);

        if (session()->has('guest_session_token')) {
            Student::where('guest_session_token', session('guest_session_token'))
                ->update([
                    'user_id' => $user->id, 
                    'name' => $data['name'], 
                    'is_guest' => false,
                    // 'guest_session_token' => null
                ]);
                
            // session()->forget('guest_session_token');
        }

        return response()->json([
            'message' => 'User registered successfully', 
            'user' => $user
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
     *              @OA\Property(property="password", type="string", format="password", example="password123"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Login success",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Login success"),
     *              @OA\Property(property="token", type="string", example="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...")
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
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json([
            'message' => 'Login success',
            'token' => $token
        ], 200);
    }
}
