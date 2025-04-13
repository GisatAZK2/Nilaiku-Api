<?php
namespace App\Services;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class GuestSessionService
{
    const SESSION_KEY = 'guest_prediction_data';
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

    public function storeGuestPrediction(array $data, array $result): array
    {
        $sessionId = Session::get('guest_session_id', Str::uuid()->toString());
        Session::put('guest_session_id', $sessionId);

        $existingPredictions = Session::get(self::SESSION_KEY, []);

        $newPrediction = [
            'session_id'        => $sessionId,
            'academic_record'   => [
                'student_id'          => $data['student_id'],
                'subject_id'          => $data['subject_id'],
                'input_date'          => now(),
                'attendance'          => $data['attendance'],
                'hours_studied'       => $data['hours_studied'],
                'previous_scores'     => $data['previous_scores'],
                'sleep_hours'         => $data['sleep_hours'],
                'tutoring_sessions'   => $data['tutoring_sessions'],
                'peer_influence'      => $data['peer_influence'],
                'motivation_level'    => $data['motivation_level'],
                'teacher_quality'     => $data['teacher_quality'],
                'access_to_resources' => $data['access_to_resources'],
            ],
            'prediction_result' => [
                'prediction_date' => now(),
                'predicted_score' => $result['predicted_score'],
                'recommendation'  => $result['recommendation'],
            ],
        ];

        $existingPredictions[] = $newPrediction;
        Session::put(self::SESSION_KEY, $existingPredictions);

        // Mengambil data terbaru dari session
        $student          = Session::get(self::STUDENT_KEY);
        $latestPrediction = end($existingPredictions);
        $record           = $latestPrediction['academic_record'];
        $predictionResult = $latestPrediction['prediction_result'];

        return [
            'student'           => $student,
            'record'            => $record,
            'prediction_result' => $predictionResult,
        ];
    }

    public function getGuestPrediction(): ?array
    {
        $predictions = Session::get(self::SESSION_KEY, []);
        $student     = Session::get(self::STUDENT_KEY);

        if (empty($predictions) || ! $student) {
            return null;
        }

        return [
            'student'     => $student,
            'predictions' => $predictions,
        ];
    }

    public function clearGuestSession(): void
    {
        Session::forget(self::SESSION_KEY);
        Session::forget(self::STUDENT_KEY);
        Session::forget('guest_session_id');
    }
}
