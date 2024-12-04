<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id(); // Kolom id sebagai primary key
            $table->string('name');
            $table->enum('gender', ['Pria', 'Wanita']);
            $table->enum('age_group', ['< 20 tahun', '20-30 tahun', '31-40 tahun', '> 40 tahun']);
            $table->string('location')->nullable();
            $table->enum('outdoor_activity', ['Ya, sering (4-6 jam/hari)', 'Kadang-kadang (1-3 jam/hari)', 'Jarang (kurang dari 1 jam/hari)']);

            // Data Kondisi Kulit
            $table->text('skin_condition')->nullable();
            $table->text('skin_problems')->nullable();
            $table->enum('acne_frequency', ['1-2 Kali', '3-4 Kali', 'Selalu ada jerawat'])->nullable();
            $table->enum('sensitive_condition', ['Eksim', 'Rosacea', 'Psoriasis', 'Melasma', 'Tidak ada'])->nullable();
            $table->text('recent_skin_issues')->nullable();
            $table->enum('sensitivity_to_new_products', ['Tidak sensitif', 'Sedikit sensitif', 'Sangat sensitif']);

            // Data Riwayat Pengguna
            $table->text('current_products')->nullable();
            $table->enum('product_reaction', ['Cocok', 'Kulit terasa kering', 'Kulit lebih berminyak', 'Kulit iritasi/kemerahan']);
            $table->text('allergies')->nullable();
            $table->enum('retinol_usage', ['Tidak pernah', 'Jarang', 'Sering']);
            $table->enum('aha_bha_usage', ['Tidak pernah', 'Jarang', 'Sering']);
            $table->enum('doctor_prescribed_products', ['Tidak pernah', 'Jarang', 'Sering']);

            // Preferensi Pengguna
            $table->enum('skincare_priority', ['Mengatasi masalah kulit', 'Mencerahkan kulit', 'Anti-aging', 'Hidrasi/pelembapan', 'Produk yang natural/vegan']);
            $table->enum('budget', ['< Rp 100.000', 'Rp 100.000 - Rp 300.000', 'Rp 300.000 - Rp 500.000', '> Rp 500.000']);
            $table->enum('preferred_product_type', ['Gel', 'Krim', 'Serum cair', 'Foam/busa']);

            // Data Lingkungan dan Gaya Hidup
            $table->enum('sun_exposure', ['Banyak (4-6 jam)', 'Sedang (1-3 jam)', 'Sedikit (< 1 jam)']);
            $table->enum('sleep_hours', ['< 4 jam', '4-6 jam', '6-8 jam', '> 8 jam']);
            $table->enum('smoking_status', ['Ya', 'Tidak']);
            $table->enum('transportation_mode', ['Mobil', 'Sepeda motor', 'Bus', 'Kereta', 'Jalan kaki']);
            $table->enum('diet', ['Bebas susu', 'Bebas gluten', 'Pescatarian', 'Aku tidak punya diet khusus']);

            // Selfie Images
            $table->string('selfie_right')->nullable();
            $table->string('selfie_left')->nullable();
            $table->string('selfie_closeup')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
