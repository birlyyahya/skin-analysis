<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Customer::create([
            'name' => 'John Doe',
            'gender' => 'Pria',
            'age_group' => '20-30 tahun',
            'location' => 'Jakarta',
            'outdoor_activity' => 'Kadang-kadang (1-3 jam/hari)',
            'skin_condition' => ['Berminyak', 'Kering'],
            'skin_problems' => 'Jerawat, Pori-pori besar',
            'acne_frequency' => '3-4 Kali',
            'sensitive_condition' => 'Tidak ada',
            'recent_skin_issues' => 'Gatal, kemerahan',
            'sensitivity_to_new_products' => 'Sedikit sensitif',
            'product_reaction' => 'Kulit terasa kering',
            'retinol_usage' => 'Jarang',
            'aha_bha_usage' => 'Jarang',
            'doctor_prescribed_products' => 'Tidak pernah',
            'skincare_priority' => 'Mengatasi masalah kulit',
            'budget' => 'Rp 100.000 - Rp 300.000',
            'preferred_product_type' => 'Gel',
            'sun_exposure' => 'Sedang (1-3 jam)',
            'sleep_hours' => '6-8 jam',
            'smoking_status' => 'Tidak',
            'transportation_mode' => 'Mobil',
            'diet' => 'Aku tidak punya diet khusus',
            'current_products' => ['Sabun wajah', 'Toner', 'Serum'],
            'allergies' => ['Fragrance', 'Alkohol'],
            'selfie_right' => 'uploads/selfies/john_doe_right.jpg',
            'selfie_left' => 'uploads/selfies/john_doe_left.jpg',
            'selfie_closeup' => 'uploads/selfies/john_doe_closeup.jpg',
        ]);

        Customer::create([
            'name' => 'Jane Smith',
            'gender' => 'Wanita',
            'age_group' => '31-40 tahun',
            'location' => 'Surabaya',
            'outdoor_activity' => 'Jarang (kurang dari 1 jam/hari)',
            'skin_condition' => ['Sensitif', 'Kombinasi'],
            'skin_problems' => 'Kulit kusam, Pori-pori besar',
            'acne_frequency' => '1-2 Kali',
            'sensitive_condition' => 'Rosacea',
            'recent_skin_issues' => 'Kemerahan',
            'sensitivity_to_new_products' => 'Sangat sensitif',
            'product_reaction' => 'Kulit lebih berminyak',
            'retinol_usage' => 'Sering',
            'aha_bha_usage' => 'Sering',
            'doctor_prescribed_products' => 'Jarang',
            'skincare_priority' => 'Hidrasi/pelembapan',
            'budget' => '< Rp 100.000',
            'preferred_product_type' => 'Serum cair',
            'sun_exposure' => 'Sedikit (< 1 jam)',
            'sleep_hours' => '> 8 jam',
            'smoking_status' => 'Ya',
            'transportation_mode' => 'Bus',
            'diet' => 'Bebas susu',
            'current_products' => ['Moisturizer', 'Facial oil'],
            'allergies' => ['Paraben'],
            'selfie_right' => 'uploads/selfies/jane_smith_right.jpg',
            'selfie_left' => 'uploads/selfies/jane_smith_left.jpg',
            'selfie_closeup' => 'uploads/selfies/jane_smith_closeup.jpg',
        ]);
    }
}
