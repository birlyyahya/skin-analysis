<?php

namespace App\Providers;

use App\Services\AnthropicAIService;
use Illuminate\Support\ServiceProvider;
use App\Services\FacePlusPlusService;
use App\Services\OpenAIService;
use App\Services\WhatsappService;

class ApiServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
        // Bind Face++ API Service
        $this->app->singleton(FacePlusPlusService::class, function ($app) {
            return new FacePlusPlusService(
                config('services.facepp.api_key'),
                config('services.facepp.api_secret'),
                config('services.facepp.endpoint')
            );
        });

        $this->app->singleton(AnthropicAIService::class, function ($app) {
            return new AnthropicAIService(
                config('services.openai.api_key'),
                config('services.openai.endpoint')
            );
        });

        $this->app->singleton(WhatsappService::class, function ($app) {
            return new WhatsappService(
                config('services.whatsapp.api_key'),
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot()
    {
        //
    }
}
