<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalPushed extends Model
{
    use HasFactory;

    protected $table = 'medicalpushed';

    protected $fillable = [
        'reg_number',
        'firstnames',
        'surname',
        'dob',
        'mobile',
        'nationalid',
        'gender',
        'address',
        'api_response',
        'status',
        'source',
        'error_message'
    ];

    protected $casts = [
        'api_response' => 'array',
        'dob' => 'date'
    ];

    // Accessor to get full name
    public function getFullNameAttribute()
    {
        return trim($this->firstnames . ' ' . $this->surname);
    }

    // Check if record was added manually
    public function isManual()
    {
        return $this->source === 'manual';
    }

    // Check if record was added via API
    public function isFromApi()
    {
        return $this->source === 'api';
    }
}
