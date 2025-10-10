<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudyImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'original_path',
        'processed_path',
        'method',
        'features_text',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}


