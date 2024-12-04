<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FacePlusPlusService
{
    protected $apiKey;
    protected $apiSecret;
    protected $endpoint;

    public function __construct($apiKey, $apiSecret, $endpoint)
    {
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
        $this->endpoint = $endpoint;
    }

    public function analyzeFace($imageUrl, $type)
    {
        $response = Http::asForm()->post($this->endpoint, [
            'api_key' => $this->apiKey,
            'api_secret' => $this->apiSecret,
            'image_url' => $imageUrl,
        ]);

        if ($response->failed()) {
            throw new \Exception('Face++ API Error: ' . $response->json('error_message', 'Unknown error'));
        }
        $result = $response->json(); // Ambil hasil JSON dari API
        $result['type'] = $type;

        return $result;
    }
    function processFacePPAnalysis($facePPResult)
    {
        try {
            if (!isset($facePPResult->result) || empty($facePPResult->result)) {
                return [
                    'skinConcerns' => [],
                    'confidenceScores' => []
                ];
            }

            $result = $facePPResult->result;
            $skinConcerns = [];
            $confidenceScores = [];

            // Fungsi helper untuk menambahkan concern berdasarkan nilai dan confidence
            $addConcern = function ($key, $value, $confidence, $threshold = 0.7) use (&$skinConcerns, &$confidenceScores) {
                if ($confidence > $threshold) {
                    $confidenceScores[$key] = $confidence;
                    if ($value == 1 || $value === true) {
                        switch ($key) {
                            case 'pores_left_cheek':
                            case 'pores_right_cheek':
                            case 'pores_forehead':
                                $skinConcerns[] = 'Pori-pori besar';
                                break;
                            case 'acne':
                                $skinConcerns[] = 'Jerawat aktif';
                                break;
                            case 'skin_spot':
                                $skinConcerns[] = 'Flek/noda kulit';
                                break;
                            case 'dark_circle':
                                $skinConcerns[] = 'Lingkaran hitam';
                                break;
                            case 'mole':
                                $skinConcerns[] = 'Tahi lalat';
                                break;
                        }
                    }
                }
            };
            foreach ($result as $key => $value) {
                if (isset($value->confidence) && isset($value->value)) {
                    $addConcern($key, $value->value, $value->confidence);
                }
            }

            // Analisis tipe kulit
            if (isset($result->skin_type->skin_type)) {
                $skinTypes = [
                    0 => 'Kulit Normal',
                    1 => 'Kulit Kering',
                    2 => 'Kulit Berminyak',
                    3 => 'Kulit Kombinasi'
                ];
                $skinType = $result->skin_type->skin_type;
                $skinConcerns[] = $skinTypes[$skinType] ?? 'Tipe kulit tidak teridentifikasi';
            }
            return [
                'skinConcerns' => array_values(array_unique($skinConcerns)), // Menghilangkan duplikat
                'confidenceScores' => $confidenceScores
            ];
        } catch (\Exception $e) {
            return [
                'skinConcerns' => ['Error dalam analisis kulit'],
                'confidenceScores' => []
            ];
        }
    }
}
