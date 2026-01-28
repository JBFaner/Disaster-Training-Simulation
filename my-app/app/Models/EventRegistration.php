<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'simulation_event_id',
        'status',
        'rejection_reason',
        'registered_at',
        'approved_at',
        'rejected_at',
        'approved_by',
    ];

    protected $casts = [
        'registered_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function simulationEvent()
    {
        return $this->belongsTo(SimulationEvent::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function attendance()
    {
        return $this->hasOne(Attendance::class);
    }
}



