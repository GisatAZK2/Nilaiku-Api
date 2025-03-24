<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Routing\Controllers\Middleware;

class StudentController extends Controller
{
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
     * Registrasi user baru
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:siswa,guru,admin,guest',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'is_guest' => $validated['role'] === 'guest',
        ]);

        if ($validated['role'] === 'siswa') {
            Student::create([
                'user_id' => $user->id,
                'name' => $validated['name'],
                'date_of_birth' => now()->subYears(15),
                'gender' => 'Laki-laki',
            ]);
        }

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user
        ]);
    }
}
