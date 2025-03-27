<?php
namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|unique:users',
            'password' => 'required|min:6',
        ]);

        $user = User::create([
            'name'    => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'siswa'
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
        ]);
    }

    public function login()
    {
        //for login
        $credentials = request(['email', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    
        return response()->json([
            'message' => 'Login success',
            'token' => $token
        ]);


    }
}
