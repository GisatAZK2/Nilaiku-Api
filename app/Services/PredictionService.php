<?php
namespace App\Services;

use GuzzleHttp\Client;

class PredictionService
{
    protected $mlApiUrl;
    protected $client;

    public function __construct()
    {
        // URL endpoint model ML (Flask)
        $this->mlApiUrl = config('services.ml_api.url');
        $this->client   = new Client();
    }

    public function getPrediction(array $inputData)
    {
        try {
            $jsonInputData = json_encode($inputData);
            $response      = $this->client->post($this->mlApiUrl, [
                'body'    => $jsonInputData,
                'timeout' => 30,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . config('services.ml_api.key')
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
}
