<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalysisFaceppResult extends Model
{
    /** @use HasFactory<\Database\Factories\AnalysisFaceppResultFactory> */
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'result_closeup',
        'result_right',
        'result_left'
    ];

    protected $casts = [
        'result_closeup',
        'result_right',
        'result_left',
    ];

    public function customer() {

        return $this->belongsTo(Customer::class);
    }


}
