<?php
namespace App\Services;

use GuzzleHttp\Client;

class PredictionService
{
    protected $mlApiUrl;
    protected $client;

    public function __construct()
    {
        // URL endpoint model ML (misal Python Flask/FastAPI)
        $this->mlApiUrl = config('services.ml_prediction.url');
        $this->client = new Client();
    }

    public function getPrediction(array $inputData)
    {
        try {
            $response = $this->client->post($this->mlApiUrl, [
                'json' => $inputData,
                'timeout' => 30
            ]);

            $predictionResult = json_decode($response->getBody(), true);

            if (!isset($predictionResult['predicted_score'])) {
                throw new \Exception('Format respons model ML tidak valid');
            }

            return $predictionResult;

        } catch (\Exception $e) {
            \Log::error('ML Prediction Error: ' . $e->getMessage());
            throw new \Exception('Gagal melakukan prediksi');
        }
    }
}