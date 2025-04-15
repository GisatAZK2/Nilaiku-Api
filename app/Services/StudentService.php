<?php

namespace App\Services;

use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class StudentService
{
    const STUDENT_KEY = 'guest_student_data';

    public function storeGuestStudentData(array $data)
    {
        $sessionId = Session::get('guest_session_id', Str::uuid()->toString());
        $studentData = [
            'name'                => $data['name'],
            'age'                 => $data['age'],
            'gender'              => $data['gender'],
            'education'           => $data['education'],
            'is_guest'            => true,
            'guest_session_token' => $sessionId,
        ];

        Session::put(self::STUDENT_KEY, $studentData);
    }

    public function createOrUpdateStudent(array $data)
    {
        if (Auth::check()) {
            $student = Student::updateOrCreate(
                ['user_id' => Auth::id()],
                array_merge($data, [
                    'is_guest' => false,
                    'guest_session_token' => null,
                    'user_id' => Auth::id(),
                ])
            );
        } else {
            $sessionId = Session::get('guest_session_id', Str::uuid()->toString());
            Session::put('guest_session_id', $sessionId);

            $this->storeGuestStudentData($data);

            $student = Student::updateOrCreate(
                ['guest_session_token' => $sessionId],
                array_merge($data, [
                    'is_guest' => true,
                    'guest_session_token' => $sessionId,
                    'user_id' => null,
                ])
            );
        }

        Session::put('guest_student_id', $student->id);
        return $student;
    }

    public function getStudentId()
    {
        return Auth::check()
        ? Student::where('user_id', Auth::id())->value('id')
        : Session::get('guest_student_id');
    }

    public function getStudentData($id)
    {
        if (Auth::check()) {
            return Student::where('user_id', Auth::id())->first();
        }

        $sessionId = Session::get('guest_session_id');
        return Student::where('id', $id)->first();
    }

    public function assignStudentToUserAfterRegister($userId)
    {
        $sessionId = Session::get('guest_session_id');
        $student = Student::where('guest_session_token', $sessionId)->first();

        if ($student) {
            $student->update([
                'user_id' => $userId,
                'is_guest' => false,
                'guest_session_token' => null
            ]);
        }

        return $student;
    }
}
