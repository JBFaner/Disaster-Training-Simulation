<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SimulationEvent extends Model
{
    protected $fillable = [
        'title',
        'disaster_type',
        'description',
        'event_category',
        'status',
        'event_date',
        'start_time',
        'end_time',
        'is_recurring',
        'recurrence_pattern',
        'location',
        'building',
        'room_zone',
        'location_notes',
        'accessibility_notes',
        'exits',
        'hazard_zones',
        'assembly_points',
        'is_high_risk_location',
        'scenario_id',
        'scenario_is_required',
        'facilitators',
        'allowed_participant_types',
        'max_participants',
        'self_registration_enabled',
        'approval_required',
        'qr_code_enabled',
        'attendance_code',
        'reserved_resources',
        'safety_guidelines',
        'hazard_warnings',
        'required_ppe',
        'event_phases',
        'inject_triggers',
        'facilitator_instructions',
        'email_notifications_enabled',
        'sms_notifications_enabled',
        'notification_schedule',
        'created_by',
        'updated_by',
        'published_at',
        'completed_at',
        'actual_start_time',
        'started_by',
    ];

    protected $casts = [
        'event_date' => 'date',
        'is_recurring' => 'boolean',
        'is_high_risk_location' => 'boolean',
        'scenario_is_required' => 'boolean',
        'self_registration_enabled' => 'boolean',
        'approval_required' => 'boolean',
        'qr_code_enabled' => 'boolean',
        'email_notifications_enabled' => 'boolean',
        'sms_notifications_enabled' => 'boolean',
        'facilitators' => 'array',
        'allowed_participant_types' => 'array',
        'reserved_resources' => 'array',
        'event_phases' => 'array',
        'inject_triggers' => 'array',
        'notification_schedule' => 'array',
        'published_at' => 'datetime',
        'completed_at' => 'datetime',
        'actual_start_time' => 'datetime',
    ];

    public function scenario()
    {
        return $this->belongsTo(Scenario::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function registrations()
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function resources()
    {
        return $this->belongsToMany(Resource::class, 'event_resource')
                    ->withPivot('quantity_needed', 'quantity_assigned', 'status', 'notes')
                    ->withTimestamps();
    }

    public function assignedResources()
    {
        return $this->hasMany(ResourceEventAssignment::class, 'event_id');
    }

    public function approvedRegistrations()
    {
        return $this->registrations()->where('status', 'approved');
    }

    public function registeredParticipantsCount()
    {
        return $this->approvedRegistrations()->count();
    }

    public function isRegistrationFull()
    {
        if (!$this->max_participants) {
            return false;
        }
        return $this->registeredParticipantsCount() >= $this->max_participants;
    }

    public function evaluation()
    {
        return $this->hasOne(Evaluation::class, 'simulation_event_id');
    }
}
