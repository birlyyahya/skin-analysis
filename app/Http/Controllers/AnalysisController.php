<?php

namespace App\Http\Controllers;

use App\Models\AnalysisFaceppResult;
use App\Models\AnalysisResult;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\AnthropicAIService;
use Illuminate\Support\Facades\Http;
use App\Services\FacePlusPlusService;
use App\Services\ProductRecommendationService;

class AnalysisController extends Controller
{
    protected $faceService;
    protected $aiService;

    public function __construct(FacePlusPlusService $faceService, AnthropicAIService $aiService)
    {
        $this->faceService = $faceService;
        $this->aiService = $aiService;
    }

    public function analyze(Request $request)
    {
        $validated = $request->validate([
            'customer_data' => 'required|array',
        ]);

        try {

            // $customer = $validated['customer_data'];
            $customer = Customer::find($validated['customer_data']['id']);

            // $customer = Customer::create($customerData);

            // Analisis Wajah
            // $faceAnalysis = $this->analyzeImages($customer);
            $faceAnalysis = AnalysisFaceppResult::where('customer_id', $customer['id'])->get()->first()->toArray();
            $faceAnalysis['closeup'] = json_decode($faceAnalysis['closeup']);
            $faceAnalysis['right'] = json_decode($faceAnalysis['right']);
            $faceAnalysis['left'] = json_decode($faceAnalysis['left']);

            // Get Recommendation
            $newRequest = Request::create('/', 'POST', ['customer_id' => $customer['id']]);
            $productRecommendations = $this->getProductRecommendations($newRequest);
            // ImageAnalyze and Recommendation
            $prompt = $this->generatePrompt($validated['customer_data'], $faceAnalysis);

            // Ai Analyze
            // $aiAnalysis = $this->aiService->generateAnalysis($prompt);



            $AnyalysisResult = [
                'face_analysis' => $faceAnalysis,
                'recommendation' => $productRecommendations,
                'ai_analysis' => '',
            ];

            $hasil = $this->formatResults($customer, $AnyalysisResult);
            dd($hasil);

            return response()->json($hasil);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function analyzeImages($customerData): array
    {
        $results = [
            'closeup' => [],
            'right' => [],
            'left' => []
        ];

        if (isset($customerData->selfie_closeup)) {
            $results['closeup'] = $this->faceService->analyzeFace($customerData->selfie_closeup, 'closeup');;
        }

        if (isset($customerData['selfie_right'])) {
            $results['right'] = $this->faceService->analyzeFace($customerData->selfie_right, 'right');
        }

        if (isset($customerData['selfie_left'])) {
            $results['left'] = $this->faceService->analyzeFace($customerData->selfie_left, 'left');
        }
        // $this->SaveAnalysisFacepp($results,  $customerData);
        return $results;
    }

    public function SaveAnalysisFacepp($data, Customer $customer)
    {
        $result = new AnalysisFaceppResult();
        $result->customer_id = $customer->id;
        $result->closeup = json_encode($data['closeup']);
        $result->right = json_encode($data['right']);
        $result->left = json_encode($data['left']);
        $result->save();

        return $result;
    }

    public function getProductRecommendations(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
        ]);

        try {
            $customer = Customer::findOrFail($validated['customer_id']);
            $recommendations = app(ProductRecommendationService::class)->getRecommendations($customer);

            return $recommendations;
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function formatResults($customerData, $analysisResults)
    {
        $imageAnalysis = $analysisResults['face_analysis'] ?? [];
        $productRecommendations = $analysisResults['recommendation'] ?? [];
        $aiAnalysis = $analysisResults['ai_analysis'] ?? '';
        // Ekstrak hasil analisis kulit dari AI Analysis
        $hasilAnalisisKulit = '';

        if (!empty($aiAnalysis)) {
            // Cari bagian analisis kulit di antara tag [SKIN_ANALYSIS]
            if (preg_match('/\[SKIN_ANALYSIS\]([\s\S]*?)\[\/SKIN_ANALYSIS\]/', $aiAnalysis, $analysisMatch)) {
                $analysisContent = $analysisMatch[1] ?? '';

                // Ekstrak bagian "Masalah Utama yang Perlu Ditangani"
                if (preg_match('/2\.\s*Masalah\s*Utama[^:]*:([\s\S]*?)(?=3\.|\[\/SKIN_ANALYSIS\])/i', $analysisContent, $masalahMatch)) {
                    $masalahContent = $masalahMatch[1] ?? '';

                    // Bersihkan dan format hasil
                    $hasilAnalisisKulit = collect(explode("\n", $masalahContent))
                        ->map(fn($line) => trim($line))
                        ->filter(fn($line) => !empty($line))
                        ->join(', ');
                }
            }
        }

        // Jika tidak ada hasil dari Claude, gunakan hasil Face++
        if (empty($hasilAnalisisKulit)) {
            $facePPAnalysis = !empty($imageAnalysis['closeup']->result)
                ? $this->faceService->processFacePPAnalysis($imageAnalysis['closeup'])
                : ['skinConcerns' => [], 'confidenceScores' => []];

            $hasilAnalisisKulit = implode(', ', $facePPAnalysis['skinConcerns']);
        }


        // Format hasil akhir
        $formattedResult = [
            'date' => now()->toDateTimeString(), // Tanggal
            'name' => $customerData['name'] ?? '', // Nama
            'age' => $customerData['age_group'] ?? '', // Umur
            'whatsapp' => $customerData['whatsapp'] ?? '', // WhatsApp
            // 'raw_facepp_result' => json_encode($imageAnalysis['closeup']->result ?? [], JSON_PRETTY_PRINT), // Raw Face++ Result
            'ai_analysis' => $aiAnalysis, // Claude Analysis
            'skin_analysis' => $hasilAnalisisKulit, // Hasil Analisis Kulit dari Claude
            'product_recommendations' => collect($productRecommendations)
                ->pluck('name')
                ->filter()
                ->join(glue: ', ') // Product Recommendations
        ];
        $this->store($customerData, $formattedResult);
        dd($formattedResult);
        // Kembalikan hasil yang diformat
        return $formattedResult;
    }

    public function store(Customer $customer, $data) {
        $product = new AnalysisResult();
        $product->customer_id = $customer->id;
        $product->face_analysis = json_encode($data['skin_analysis']);
        $product->ai_analysis = json_encode($data['ai_analysis']);
        $product->recommended_products = json_encode($data['product_recommendations']);
        $product->save($data);
    }


    private function generatePrompt(array $customerData, array $faceAnalysis): string
    {
        // Format data hasil deep learning
        $skinCheck = $this->faceService->processFacePPAnalysis($faceAnalysis['closeup']);
        $skinConcerns = $skinCheck['skinConcerns'] ?? ['Tidak ada masalah terdeteksi'];
        $additionalDetails = json_encode($faceAnalysis['closeup']->result ?? [], JSON_PRETTY_PRINT);

        return "
        Anda bertugas sebagai ahli perawatan kulit LarasAI, dengan tujuan memberikan analisis menyeluruh dan rekomendasi yang dipersonalisasi untuk pelanggan berdasarkan data yang diberikan. Dalam konteks ini, Anda akan menggunakan informasi tentang kondisi kulit, gaya hidup, dan hasil analisis dari deep learning untuk menyusun laporan yang informatif dan bermanfaat.

        Berikut adalah data pelanggan yang perlu Anda analisis:

        Nama: {$customerData['name']}
        Umur: {$customerData['age_group']}
        Jenis Kelamin: {$customerData['gender']}
        Kondisi Kulit: " . implode(", ", $customerData['skin_condition']) . "
        Keinginan: {$customerData['skincare_priority']}
        Masalah Kulit: {$customerData['skin_problems']}
        Kondisi Khusus: {$customerData['sensitive_condition']}
        Alergi: " . implode(", ", $customerData['allergies']) . "

        Informasi tambahan mengenai gaya hidup pelanggan:

        Paparan Matahari: {$customerData['sun_exposure']}
        Jam Tidur: {$customerData['sleep_hours']}
        Transportasi: {$customerData['transportation_mode']}
        Diet: {$customerData['diet']}

        Hasil analisis kulit dari deep learning yang harus Anda pertimbangkan adalah: " . implode(", ", $skinConcerns) . "

        Detail tambahan dari analisis deep learning termasuk: $additionalDetails

        Silakan berikan analisis dalam format berikut sertakan akurasi analisa hasil kulit:
        [SKIN_ANALYSIS]
        1. Analisis Kondisi Kulit Saat Ini:
        Kulit pelanggan memiliki jenis [tuliskan jenis kulit, misalnya berminyak/kering/normal]. Berdasarkan analisis dan keluhan yang dilaporkan, masalah utama meliputi [sebutkan masalah seperti jerawat, noda hitam, atau garis halus]. Area yang perlu perhatian khusus adalah [tuliskan area spesifik seperti pipi, dahi, atau dagu].

        2. Masalah Utama yang Perlu Ditangani:
        [Masalah 1, misalnya: Kulit kusam akibat paparan matahari.]
        [Masalah 2, misalnya: Jerawat aktif di area T-zone.]
        [Masalah lainnya berdasarkan analisis deep learning.]

        3. Rekomendasi Perawatan Kulit:
        Gunakan pembersih wajah dengan [bahan yang disarankan, seperti asam salisilat].
        Terapkan pelembap ringan yang cocok untuk kulit [jenis kulit].
        Gunakan sunscreen SPF [angka SPF] untuk melindungi dari paparan matahari.
        Tambahkan serum dengan kandungan [contoh: vitamin C] untuk mencerahkan kulit.

        4. Saran Gaya Hidup:
        Tingkatkan jam tidur hingga [minimal 7-8 jam per malam].
        Kurangi paparan matahari langsung dengan menggunakan pelindung seperti topi atau payung.
        Perbaiki pola makan dengan menambahkan [contoh: sayuran hijau, kacang-kacangan, omega-3].
        Hindari stres berlebih untuk mengurangi risiko jerawat.

        5. Catatan Khusus:
        Pelanggan memiliki alergi terhadap [sebutkan alergen], sehingga hindari produk dengan kandungan tersebut. Perhatikan juga kondisi khusus seperti [misalnya, kehamilan] dalam memilih produk perawatan kulit.
        [/SKIN_ANALYSIS]
    ";
    }
    function calculateAge($birthDate)
    {
        if (!$birthDate) {
            return null;
        }
        return now()->diffInYears(\Carbon\Carbon::parse($birthDate));
    }
}
