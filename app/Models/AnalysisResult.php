<?php

namespace App\Models;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AnalysisResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'face_analysis',
        'ai_analysis',
        'recommended_products',
    ];

    public function saveResult($data) {
        return $this->create($data);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

}
