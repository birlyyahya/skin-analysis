<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'gender',
        'age_group',
        'location',
        'skin_condition',
        'outdoor_activity',
        'skin_problems',
        'acne_frequency',
        'sensitive_condition',
        'recent_skin_issues',
        'sensitivity_to_new_products',
        'current_products',
        'product_reaction',
        'allergies',
        'retinol_usage',
        'aha_bha_usage',
        'doctor_prescribed_products',
        'skincare_priority',
        'budget',
        'preferred_product_type',
        'sun_exposure',
        'sleep_hours',
        'smoking_status',
        'transportation_mode',
        'diet',
        'selfie_right',
        'selfie_left',
        'selfie_closeup',
    ];

    protected $casts = [
        'skin_condition' => 'array',
        'allergies' => 'array',
        'current_products' => 'array',
    ];

    public function analysisResults()
    {
        return $this->hasMany(AnalysisResult::class);
    }
    public function analysisFaceppResult()
    {
        return $this->belongsTo(AnalysisFaceppResult::class);
    }
}
