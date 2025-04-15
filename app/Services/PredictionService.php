<?php
namespace App\Services;

use App\Models\AcademicRecord;
use App\Models\PredictionResult;
use App\Models\Student;
use GuzzleHttp\Client;

class PredictionService
{
    protected $mlApiUrl;
    protected $mlApiKey;
    protected $client;

    public function __construct()
    {
        // URL endpoint model ML (Flask)
        $this->mlApiUrl = config('services.ml_api.url');
        $this->mlApiKey = config('services.ml_api.key');
        $this->client   = new Client();
    }

    public function getPrediction(array $inputData)
    {
        try {
            $jsonInputData = json_encode($inputData);
            $response      = $this->client->post($this->mlApiUrl . '/predict', [
                'body'    => $jsonInputData,
                'timeout' => 30,
                'headers' => [
                    'Content-Type'  => 'application/json',
                    'Authorization' => 'Bearer ' . $this->mlApiKey,
                ],
            ]);

            $predictionResult = json_decode($response->getBody(), true);

            if (! isset($predictionResult['predicted_score'])) {
                throw new \Exception('Format respons model ML tidak valid');
            }

            return $predictionResult;
        } catch (\Exception $e) {
            \Log::error('Kesalahan Prediksi ML: ' . $e->getMessage());
            throw new \Exception('Gagal melakukan prediksi');
        }
    }

    public function storePrediction(array $data, array $result, $userIdOrGuestToken): array
    {
        $isGuest = ! is_numeric($userIdOrGuestToken);

        // $student = Student::where($isGuest ? 'guest_session_token' : 'user_id', $userIdOrGuestToken)->first();
        $student = Student::findorFail($data['student_id']);

        $record = AcademicRecord::create([
            'student_id'          => $student->id ?? $data['student_id'],
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
        ]);

        $predictionResult = PredictionResult::create([
            'record_id'       => $record->id,
            'prediction_date' => now(),
            'predicted_score' => $result['predicted_score'],
            'recommendation'  => $result['recommendation'],
        ]);

        return [
            'student'           => $student,
            'record'            => $record,
            'prediction_result' => $predictionResult,
        ];
    }
}
