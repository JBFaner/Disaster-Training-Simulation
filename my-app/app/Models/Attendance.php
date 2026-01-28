<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_registration_id',
        'user_id',
        'simulation_event_id',
        'check_in_method',
        'status',
        'checked_in_at',
        'checked_out_at',
        'notes',
        'is_locked',
        'marked_by',
    ];

    protected $casts = [
        'checked_in_at' => 'datetime',
        'checked_out_at' => 'datetime',
        'is_locked' => 'boolean',
    ];

    public function eventRegistration()
    {
        return $this->belongsTo(EventRegistration::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function simulationEvent()
    {
        return $this->belongsTo(SimulationEvent::class);
    }

    public function marker()
    {
        return $this->belongsTo(User::class, 'marked_by');
    }
}



