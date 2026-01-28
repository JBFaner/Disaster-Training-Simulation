<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangayProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'barangay_name',
        'municipality_city',
        'province',
        'barangay_address',
        'contact_number',
        'email_address',
        'estimated_population',
        'area_classification',
        'hazards',
        'hazard_notes',
    ];

    protected $casts = [
        'hazards' => 'array',
        'estimated_population' => 'integer',
    ];
}
