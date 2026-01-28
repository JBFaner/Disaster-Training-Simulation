<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    protected $fillable = [
        'simulation_event_id',
        'status',
        'pass_threshold',
        'overall_notes',
        'created_by',
        'updated_by',
        'started_at',
        'completed_at',
        'locked_at',
    ];

    protected $casts = [
        'pass_threshold' => 'decimal:2',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'locked_at' => 'datetime',
    ];

    public function simulationEvent()
    {
        return $this->belongsTo(SimulationEvent::class, 'simulation_event_id');
    }

    public function participantEvaluations()
    {
        return $this->hasMany(ParticipantEvaluation::class, 'evaluation_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function isLocked()
    {
        return $this->status === 'locked' || $this->locked_at !== null;
    }

    public function canBeEdited()
    {
        return !$this->isLocked() && $this->status !== 'completed';
    }
}
