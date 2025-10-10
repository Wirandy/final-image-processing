<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'action',
        'user_id',
        'patient_id',
        'study_image_id',
        'ip',
        'user_agent',
        'meta',
    ];
}


