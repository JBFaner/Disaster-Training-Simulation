<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Resource extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'description',
        'quantity',
        'available',
        'condition',
        'status',
        'location',
        'serial_number',
        'image_url',
        'assigned_to_event_id',
        'assigned_handler_id',
        'maintenance_status',
        'last_maintenance_date',
        'last_inspection_date',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'last_maintenance_date' => 'datetime',
        'last_inspection_date' => 'datetime',
        'quantity' => 'integer',
        'available' => 'integer',
    ];

    /**
     * Get all event assignments for this resource
     */
    public function eventAssignments()
    {
        return $this->hasMany(ResourceEventAssignment::class);
    }

    /**
     * Get all events this resource is assigned to
     */
    public function events()
    {
        return $this->belongsToMany(SimulationEvent::class, 'resource_event_assignments', 'resource_id', 'event_id')
                    ->withPivot('quantity_assigned', 'status', 'notes', 'assigned_by', 'returned_by', 'returned_at')
                    ->withTimestamps();
    }

    /**
     * Planned events via event_resource planning pivot
     */
    public function plannedEvents()
    {
        return $this->belongsToMany(SimulationEvent::class, 'event_resource')
                    ->withPivot('quantity_needed', 'quantity_assigned', 'status', 'notes')
                    ->withTimestamps();
    }

    /**
     * Get the simulation event this resource is assigned to
     */
    public function assignedEvent()
    {
        return $this->belongsTo(SimulationEvent::class, 'assigned_to_event_id');
    }

    /**
     * Get the user who was assigned as handler
     */
    public function assignedHandler()
    {
        return $this->belongsTo(User::class, 'assigned_handler_id');
    }

    /**
     * Get the user who created this resource
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this resource
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the maintenance logs for this resource
     */
    public function maintenanceLogs()
    {
        return $this->hasMany(ResourceMaintenanceLog::class)->orderBy('created_at', 'desc');
    }

    /**
     * Check if resource is available
     */
    public function isAvailable(): bool
    {
        return $this->status === 'Available' && $this->available > 0;
    }

    /**
     * Check if resource needs repair
     */
    public function needsRepair(): bool
    {
        return $this->condition === 'Needs Repair' || $this->status === 'Damaged';
    }

    /**
     * Get total quantity assigned to events
     */
    public function getTotalAssignedQuantity(): int
    {
        try {
            return (int) $this->eventAssignments()
                ->where('status', 'Active')
                ->sum('quantity_assigned');
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get available quantity (quantity - assigned)
     */
    public function getAvailableQuantity(): int
    {
        // If the new assignment system isn't set up yet, fall back to the old 'available' field
        try {
            $total = $this->getTotalAssignedQuantity();
            return $this->quantity - $total;
        } catch (\Exception $e) {
            return $this->available ?? $this->quantity;
        }
    }

    /**
     * Assign resource to an event (supports multiple assignments)
     */
    public function assignToEvent(SimulationEvent $event, int $quantity = 1, ?User $handler = null): bool
    {
        // Use assignment table if available; fall back to 'available' field
        $currentAvailable = $this->getAvailableQuantity();
        if ($currentAvailable < $quantity) {
            return false;
        }

        return DB::transaction(function () use ($event, $quantity, $handler) {
            // Create or update an assignment record to track this allocation
            try {
                ResourceEventAssignment::updateOrCreate(
                    [
                        'resource_id' => $this->id,
                        'event_id' => $event->id,
                    ],
                    [
                        'quantity_assigned' => $quantity,
                        'status' => 'Active',
                        'notes' => null,
                        'assigned_by' => auth()->id(),
                    ]
                );
            } catch (\Exception $e) {
                // If assignment table isn't ready, continue with legacy fields
            }

            // Compute new availability based on total assigned
            $newTotalAssigned = $this->getTotalAssignedQuantity();
            $newAvailable = max(0, $this->quantity - $newTotalAssigned);

            // Update resource status and handler
            $this->update([
                'available' => $newAvailable,
                'status' => $newAvailable > 0 ? 'Partially Assigned' : 'Fully Assigned',
                'assigned_to_event_id' => $event->id,
                'assigned_handler_id' => $handler ? $handler->id : auth()->id(),
            ]);

            // Log the assignment
            ResourceMaintenanceLog::create([
                'resource_id' => $this->id,
                'action' => 'assigned_to_event',
                'notes' => "Assigned {$quantity} unit(s) to event: {$event->title}",
                'recorded_by' => auth()->id(),
            ]);

            return true;
        });
    }

    /**
     * Return resource from a specific event
     */
    public function returnFromEvent(?string $condition = null, ?string $damageReport = null): bool
    {
        // Reset available quantity to full and mark as available
        $this->update([
            'available' => $this->quantity,
            'status' => 'Available',
            'assigned_to_event_id' => null,
            'assigned_handler_id' => null,
            'condition' => $condition ?? $this->condition,
        ]);

        if ($damageReport) {
            ResourceMaintenanceLog::create([
                'resource_id' => $this->id,
                'action' => 'returned_with_damage',
                'notes' => $damageReport,
                'recorded_by' => auth()->id(),
            ]);
        } else {
            ResourceMaintenanceLog::create([
                'resource_id' => $this->id,
                'action' => 'returned_from_event',
                'notes' => 'Resource returned in good condition',
                'recorded_by' => auth()->id(),
            ]);
        }

        return true;
    }

    /**
     * Schedule maintenance
     */
    public function scheduleMaintenance(string $notes, ?string $technician = null): bool
    {
        $this->update([
            'status' => 'Under Maintenance',
            'maintenance_status' => 'Scheduled',
        ]);

        ResourceMaintenanceLog::create([
            'resource_id' => $this->id,
            'action' => 'maintenance_scheduled',
            'notes' => $notes,
            'technician' => $technician,
            'recorded_by' => auth()->id(),
        ]);

        return true;
    }

    /**
     * Complete maintenance
     */
    public function completeMaintenance(string $condition, ?string $notes = null): bool
    {
        $this->update([
            'status' => 'Available',
            'condition' => $condition,
            'maintenance_status' => 'Completed',
            'last_maintenance_date' => now(),
        ]);

        ResourceMaintenanceLog::create([
            'resource_id' => $this->id,
            'action' => 'maintenance_completed',
            'notes' => $notes,
            'recorded_by' => auth()->id(),
        ]);

        return true;
    }

    /**
     * Scope: only resources currently available
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'Available')->where('available', '>', 0);
    }

    /**
     * Scope: resources under maintenance
     */
    public function scopeUnderMaintenance($query)
    {
        return $query->where('status', 'Under Maintenance');
    }

    /**
     * Scope: resources needing repair
     */
    public function scopeNeedsRepair($query)
    {
        return $query->where(function ($q) {
            $q->where('condition', 'Needs Repair')->orWhere('status', 'Damaged');
        });
    }

    /**
     * Scope: resources currently assigned to any event
     */
    public function scopeAssigned($query)
    {
        return $query->whereNotNull('assigned_to_event_id');
    }
}
