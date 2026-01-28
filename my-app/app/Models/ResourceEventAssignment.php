<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResourceEventAssignment extends Model
{
    protected $table = 'resource_event_assignments';

    protected $fillable = [
        'resource_id',
        'event_id',
        'quantity_assigned',
        'status',
        'notes',
        'assigned_by',
        'returned_by',
        'returned_at',
        'assigned_at',
        'expected_return_date',
    ];

    protected $dates = [
        'returned_at',
        'assigned_at',
        'expected_return_date',
        'created_at',
        'updated_at',
    ];

    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }

    public function event()
    {
        return $this->belongsTo(SimulationEvent::class, 'event_id');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function returnedBy()
    {
        return $this->belongsTo(User::class, 'returned_by');
    }
}
