<?php

namespace Tests\Feature;

use Log;
use Tests\TestCase;
use App\Services\AnthropicAIService;
use Illuminate\Support\Facades\Http;
use App\Services\FacePlusPlusService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AnalysisApiTest extends TestCase
{
    // public function test_faceplusplus_api_key_is_valid()
    // {
    //     $service = app(abstract: FacePlusPlusService::class);
    //     // Simulate a valid image URL (publicly accessible image)
    //     $testImageUrl = 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?q=80&w=1887&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D';

    //     // Perform a fake HTTP call

    //     try {
    //         $response = $service->analyzeFace($testImageUrl);

    //         $this->assertNotEmpty($response, 'Response from Facepp should not be empty.');
    //     } catch (\Exception $e) {
    //         $this->fail('FacePP API call failed: ' . $e->getMessage());
    //     }

    // }

    /**
     * Test OpenAI API key validity.
     */
    public function test_openai_api_key_is_valid()
    {
        $service = app(AnthropicAIService::class);

        $prompt = 'This is a test prompt for OpenAI.';

        try {
            // Jalankan generate analysis
            $response = $service->generateAnalysis($prompt);

            // Assertions yang lebih tepat
            $this->assertIsString($response, 'Response should be a string');
            $this->assertNotEmpty($response, 'Response should not be empty');

        } catch (\Exception $e) {
            $this->fail('Anthropic API call failed: ' . $e->getMessage());
        }
    }

}
