<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Scenario extends Model
{
    protected $fillable = [
        'title',
        'short_description',
        'affected_area',
        'incident_time',
        'incident_time_text',
        'general_situation',
        'severity_level',
        'disaster_type',
        'difficulty',
        'intended_participants',
        'safety_notes',
        'weather',
        'location_type',
        'casualty_count',
        'injured_victims_count',
        'trapped_persons_count',
        'infrastructure_damage',
        'communication_status',
        'learning_objectives',
        'target_competencies',
        'criteria',
        'training_module_id',
        'is_required_for_module',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'criteria' => 'array',
    ];

    public function trainingModule()
    {
        return $this->belongsTo(TrainingModule::class, 'training_module_id');
    }

    public function injects()
    {
        return $this->hasMany(ScenarioInject::class)->orderBy('trigger_time_text');
    }

    public function expectedActions()
    {
        return $this->hasMany(ScenarioExpectedAction::class)->orderBy('order');
    }
}
