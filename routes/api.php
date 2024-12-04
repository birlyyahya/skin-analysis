<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnalysisController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/analyze', [AnalysisController::class, 'analyze']);
Route::post('/product-recommendations', [AnalysisController::class, 'getProductRecommendations']);
