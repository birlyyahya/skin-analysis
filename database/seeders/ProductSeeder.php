<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::create([
            'name' => 'Moisturizer A',
            'ingredients' => 'water, glycerin, paraben',
            'benefits' => 'hydration, sensitive, acne',
            'price' => 50000,
        ]);

        Product::create([
            'name' => 'Sunscreen B',
            'ingredients' => 'zinc oxide, glycerin',
            'benefits' => 'UV protection, hydration',
            'price' => 75000,
        ]);
        Product::create([
            'name' => 'Cleanser A',
            'ingredients' => 'water, salicylic acid, glycerin',
            'benefits' => 'cleansing, acne, sensitive',
            'price' => 40000,
        ]);

        Product::create([
            'name' => 'Toner B',
            'ingredients' => 'aloe vera, witch hazel, glycerin',
            'benefits' => 'hydration, soothing, brightening',
            'price' => 45000,
        ]);

        Product::create([
            'name' => 'Serum C',
            'ingredients' => 'niacinamide, zinc, hyaluronic acid',
            'benefits' => 'brightening, acne, hydration',
            'price' => 85000,
        ]);

        Product::create([
            'name' => 'Sunscreen D',
            'ingredients' => 'titanium dioxide, glycerin, aloe vera',
            'benefits' => 'UV protection, hydration, sensitive',
            'price' => 65000,
        ]);

        Product::create([
            'name' => 'Moisturizer E',
            'ingredients' => 'ceramide, hyaluronic acid, shea butter',
            'benefits' => 'hydration, sensitive, dry skin',
            'price' => 70000,
        ]);

        Product::create([
            'name' => 'Eye Cream F',
            'ingredients' => 'caffeine, peptides, glycerin',
            'benefits' => 'hydration, anti-aging, dark circles',
            'price' => 95000,
        ]);

        Product::create([
            'name' => 'Exfoliant G',
            'ingredients' => 'AHA, BHA, salicylic acid',
            'benefits' => 'exfoliation, acne, brightening',
            'price' => 80000,
        ]);

        Product::create([
            'name' => 'Mask H',
            'ingredients' => 'charcoal, kaolin clay, glycerin',
            'benefits' => 'cleansing, oil control, acne',
            'price' => 55000,
        ]);

        Product::create([
            'name' => 'Face Oil I',
            'ingredients' => 'jojoba oil, squalane, rosehip oil',
            'benefits' => 'hydration, anti-aging, soothing',
            'price' => 85000,
        ]);

        Product::create([
            'name' => 'Lip Balm J',
            'ingredients' => 'beeswax, shea butter, vitamin E',
            'benefits' => 'hydration, soothing, repair',
            'price' => 20000,
        ]);
    }
}
