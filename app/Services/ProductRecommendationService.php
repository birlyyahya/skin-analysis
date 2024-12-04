<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Customer;

class ProductRecommendationService
{
    /**
     * Create a new class instance.
     */
    public function getRecommendations(Customer $customer)
    {
        $products = Product::all(); // Ambil semua produk dari database

        $scoredProducts = $products->map(function ($product) use ($customer) {
            return [
                'product' => $product,
                'score' => $this->calculateProductScore($product, $customer),
            ];
        });

        // Filter produk dengan skor > 0, urutkan berdasarkan skor, dan ambil top 5
        $topProducts = $scoredProducts->filter(fn($item) => $item['score'] > 0)
            ->sortByDesc('score')
            ->take(5)
            ->pluck('product');

        return $topProducts->toArray();
    }

    protected function calculateProductScore(Product $product, Customer $customer)
    {
        $score = 0;

        // Ambil data produk
        $benefits = strtolower($product->benefits);
        $ingredients = strtolower($product->ingredients);

        // Skor berdasarkan kondisi kulit saat ini
        foreach ($customer->skin_condition as $condition) {
            if (str_contains($benefits, strtolower($condition))) {
                $score += 2;
            }
        }

        // Skor berdasarkan masalah kulit
        if ($customer->skin_problems) {
            $skinProblems = strtolower($customer->skin_problems);
            if (str_contains($skinProblems, 'flek') && str_contains($benefits, 'brightening')) {
                $score += 1;
            }
            if (str_contains($skinProblems, 'jerawat') && str_contains($benefits, 'anti-acne')) {
                $score += 2;
            }
            if (str_contains($skinProblems, 'bekas') && str_contains($benefits, 'scar healing')) {
                $score += 1.5;
            }
        }

        // Pengurangan skor jika produk mengandung alergen
        foreach ($customer->allergies as $allergen) {
            if (str_contains($ingredients, strtolower($allergen))) {
                $score -= 3;
            }
        }
        // Skor berdasarkan frekuensi jerawat
        if ($customer->acne_frequency) {
            $acneFrequency = intval($customer->acne_frequency);
            if ($acneFrequency >= 3) {
                $score += 2;
            } elseif ($acneFrequency >= 1) {
                $score += 1;
            }
        }

        // Skor berdasarkan kondisi kulit sensitif
        if ($customer->sensitive_condition && str_contains($benefits, 'sensitive')) {
            $score += 1;
        }

        // Skor berdasarkan budget
        if ($product->price <= $customer->budget) {
            $score += 1;
        }

        // Penyesuaian berdasarkan penggunaan AHA/BHA
        if ($customer->aha_bha_usage && str_contains($benefits, 'AHA') || str_contains($benefits, 'BHA')) {
            $score += 1;
        }

        // Penyesuaian berdasarkan produk yang sesuai dengan prioritas perawatan kulit
        if (str_contains($customer->skincare_priority, 'mencerahkan') && str_contains($benefits, 'brightening')) {
            $score += 1.5;
        }

        // Pastikan skor tidak negatif
        return max(0, $score); // Pastikan skor tidak negatif
    }
}
