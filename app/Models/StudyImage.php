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
        'processing_history',
        'method',
        'features_text',
        'forensic_analysis',
        'forensic_summary',
        'injury_count',
        'severity_level',
    ];

    protected $casts = [
        'forensic_analysis' => 'array',
        'processing_history' => 'array',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}


