<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use App\Models\ResourceMaintenanceLog;
use App\Models\SimulationEvent;
use App\Models\User;
use Illuminate\Http\Request;

class ResourceController extends Controller
{
    /**
     * Display a listing of all resources (Dashboard view)
     */
    public function index(Request $request)
    {
        $query = Resource::query();

        // Search by name or serial number
        if ($request->has('search')) {
            $query->where('name', 'like', "%{$request->search}%")
                  ->orWhere('serial_number', 'like', "%{$request->search}%");
        }

        // Filter by category
        if ($request->has('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by condition
        if ($request->has('condition') && $request->condition !== 'all') {
            $query->where('condition', $request->condition);
        }

        $resources = $query->with(['assignedEvent', 'assignedHandler', 'creator'])->paginate(15);

        return view('app', [
            'section' => 'resources',
            'resources' => $resources,
            'stats' => [
                'total' => Resource::count(),
                'available' => Resource::where('status', 'Available')->count(),
                'inUse' => Resource::where('status', 'In Use')->count(),
                'needsRepair' => Resource::where('condition', 'Needs Repair')->orWhere('status', 'Damaged')->count(),
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource
     */
    public function create()
    {
        return view('app', ['section' => 'resources-create']);
    }

    /**
     * Store a newly created resource
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'description' => 'nullable|string',
            'quantity' => 'required|integer|min:1',
            'condition' => 'required|in:New,Good,Needs Repair,Damaged',
            'location' => 'required|string|max:255',
            'serial_number' => 'nullable|string',
            'image_url' => 'nullable|url',
        ]);

        // Check for duplication: same name + serial_number combination
        $duplicateQuery = Resource::where('name', $validated['name'])
            ->where('category', $validated['category']);
        
        // If serial number is provided, check for exact match
        if (!empty($validated['serial_number'])) {
            $duplicateQuery->where('serial_number', $validated['serial_number']);
            
            // Also check if serial number already exists (unique constraint)
            $existingSerial = Resource::where('serial_number', $validated['serial_number'])->first();
            if ($existingSerial) {
                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'A resource with this serial number already exists: ' . $existingSerial->name,
                        'duplicate' => $existingSerial,
                    ], 422);
                }
                return back()->withErrors(['serial_number' => 'A resource with this serial number already exists: ' . $existingSerial->name])->withInput();
            }
        }
        
        // Check for duplicate name + category combination
        $duplicate = $duplicateQuery->first();
        if ($duplicate) {
            $message = 'A resource with the same name and category already exists';
            if (!empty($validated['serial_number'])) {
                $message .= '. Serial number must be unique.';
            } else {
                $message .= '. Consider adding a serial number to distinguish this resource.';
            }
            
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'duplicate' => $duplicate,
                ], 422);
            }
            return back()->withErrors(['name' => $message])->withInput();
        }

        $validated['available'] = $validated['quantity'];
        $validated['status'] = 'Available';
        $validated['created_by'] = auth()->id();
        $validated['updated_by'] = auth()->id();

        $resource = Resource::create($validated);

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'message' => 'Resource created successfully',
                'resource' => $resource,
            ], 201);
        }

        return redirect()->route('resources.index')->with('success', 'Resource created successfully');
    }

    /**
     * Display the specified resource
     */
    public function show(Resource $resource)
    {
        return view('app', [
            'section' => 'resources-show',
            'resource' => $resource->load(['assignedEvent', 'assignedHandler', 'maintenanceLogs']),
        ]);
    }

    /**
     * Show the form for editing the specified resource
     */
    public function edit(Resource $resource)
    {
        return view('app', [
            'section' => 'resources-edit',
            'resource' => $resource,
        ]);
    }

    /**
     * Update the specified resource
     */
    public function update(Request $request, Resource $resource)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'description' => 'nullable|string',
            'quantity' => 'required|integer|min:1',
            'condition' => 'required|in:New,Good,Needs Repair,Damaged',
            'status' => 'required|in:Available,In Use,Under Maintenance,Damaged,Missing,Reserved,Partially Assigned,Fully Assigned',
            'location' => 'required|string|max:255',
            'image_url' => 'nullable|url',
        ]);

        $validated['updated_by'] = auth()->id();

        $resource->update($validated);

        // Return JSON for API requests
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'message' => 'Resource updated successfully',
                'resource' => $resource,
            ]);
        }

        return redirect()->route('resources.show', $resource)->with('success', 'Resource updated successfully');
    }

    /**
     * Assign resource to a simulation event
     */
    public function assignToEvent(Request $request, Resource $resource)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:simulation_events,id',
            'handler_id' => 'nullable|exists:users,id',
            'quantity' => 'required|integer|min:1',
            'assignment_date' => 'nullable|date',
            'expected_return_date' => 'nullable|date|after_or_equal:assignment_date',
        ]);

        // Check for existing active assignments to prevent double assignment
        $existingAssignment = \App\Models\ResourceEventAssignment::where('resource_id', $resource->id)
            ->where('event_id', $validated['event_id'])
            ->where('status', 'Active')
            ->first();

        if ($existingAssignment) {
            return response()->json([
                'success' => false,
                'message' => 'This resource is already assigned to this event. Please return it first before reassigning.',
            ], 422);
        }

        // Check available quantity
        $availableQty = $resource->getAvailableQuantity();
        if ($availableQty < $validated['quantity']) {
            return response()->json([
                'success' => false,
                'message' => "Insufficient quantity. Available: {$availableQty}, Requested: {$validated['quantity']}",
            ], 400);
        }

        $event = SimulationEvent::findOrFail($validated['event_id']);
        $handler = isset($validated['handler_id']) ? User::find($validated['handler_id']) : null;

        if (!$resource->assignToEvent($event, $validated['quantity'], $handler)) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign resource to event',
            ], 400);
        }

        // Update assignment record with dates if provided
        $assignment = \App\Models\ResourceEventAssignment::where('resource_id', $resource->id)
            ->where('event_id', $validated['event_id'])
            ->where('status', 'Active')
            ->latest()
            ->first();

        if ($assignment && ($validated['assignment_date'] || $validated['expected_return_date'])) {
            $assignment->update([
                'assigned_at' => $validated['assignment_date'] ?? now(),
                'expected_return_date' => $validated['expected_return_date'] ?? null,
            ]);
        }

        // Automatically change status to "In Use"
        $resource->update(['status' => 'In Use']);

        return response()->json([
            'success' => true,
            'message' => 'Resource assigned to event successfully. Status changed to "In Use".',
        ]);
    }

    /**
     * Return resource from event
     */
    public function returnFromEvent(Request $request, Resource $resource)
    {
        $validated = $request->validate([
            'condition' => 'nullable|string',
            'damage_report' => 'nullable|string',
            'event_id' => 'nullable|exists:simulation_events,id',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $eventId = $validated['event_id'] ?? null;
        $quantity = $validated['quantity'] ?? null;

        // If event_id is provided, update the specific assignment
        if ($eventId) {
            // Update the resource_event_assignments table
            $assignment = \App\Models\ResourceEventAssignment::where('resource_id', $resource->id)
                ->where('event_id', $eventId)
                ->where('status', 'Active')
                ->first();

            if ($assignment) {
                $assignment->update([
                    'status' => 'Returned',
                    'returned_by' => auth()->id(),
                    'returned_at' => now(),
                    'notes' => $validated['damage_report'] ?? 'Returned after event completion',
                ]);
            }

            // Update the event_resource pivot table if it exists
            $resource->plannedEvents()->updateExistingPivot($eventId, [
                'status' => 'Returned',
            ]);
        }

        // Call the model's returnFromEvent method
        $resource->returnFromEvent(
            $validated['condition'] ?? null,
            $validated['damage_report'] ?? null
        );

        return response()->json([
            'success' => true,
            'message' => 'Resource returned successfully',
        ]);
    }

    /**
     * Schedule maintenance for a resource
     */
    public function scheduleMaintenance(Request $request, Resource $resource)
    {
        $validated = $request->validate([
            'notes' => 'required|string',
            'technician' => 'nullable|string|max:255',
        ]);

        $resource->scheduleMaintenance($validated['notes'], $validated['technician'] ?? null);

        return response()->json([
            'success' => true,
            'message' => 'Maintenance scheduled successfully',
        ]);
    }

    /**
     * Complete maintenance for a resource
     */
    public function completeMaintenance(Request $request, Resource $resource)
    {
        $validated = $request->validate([
            'condition' => 'required|in:New,Good,Needs Repair,Damaged',
            'notes' => 'nullable|string',
        ]);

        $resource->completeMaintenance($validated['condition'], $validated['notes'] ?? null);

        return response()->json([
            'success' => true,
            'message' => 'Maintenance completed successfully',
        ]);
    }

    /**
     * Mark resource as in-use during event
     */
    public function markInUse(Request $request, Resource $resource)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:simulation_events,id',
            'deployment_notes' => 'nullable|string',
        ]);

        $resource->update([
            'status' => 'In Use',
        ]);

        ResourceMaintenanceLog::create([
            'resource_id' => $resource->id,
            'action' => 'marked_in_use',
            'notes' => $validated['deployment_notes'] ?? 'Resource deployed during event',
            'recorded_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Resource marked as in use',
        ]);
    }

    /**
     * Mark resource as unused during event
     */
    public function markUnused(Request $request, Resource $resource)
    {
        $resource->update([
            'status' => 'Available',
        ]);

        ResourceMaintenanceLog::create([
            'resource_id' => $resource->id,
            'action' => 'marked_unused',
            'notes' => $request->input('notes') ?? 'Resource not used during event',
            'recorded_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Resource marked as unused',
        ]);
    }

    /**
     * Report damage or issues with resource
     */
    public function reportDamage(Request $request, Resource $resource)
    {
        $validated = $request->validate([
            'damage_type' => 'required|in:damaged,missing,lost',
            'description' => 'required|string',
            'severity' => 'required|in:minor,major,critical',
        ]);

        $resource->update([
            'condition' => 'Damaged',
            'status' => 'Under Maintenance',
        ]);

        ResourceMaintenanceLog::create([
            'resource_id' => $resource->id,
            'action' => 'damage_reported',
            'notes' => "[{$validated['severity']}] {$validated['damage_type']}: {$validated['description']}",
            'recorded_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Damage reported and logged',
        ]);
    }

    /**
     * Delete the specified resource
     */
    public function destroy(Resource $resource)
    {
        $resource->delete();

        return redirect()->route('resources.index')->with('success', 'Resource deleted successfully');
    }

    /**
     * Get maintenance logs for a resource
     */
    public function maintenanceLogs(Resource $resource)
    {
        return view('app', [
            'section' => 'resources-maintenance',
            'resource' => $resource,
            'logs' => $resource->maintenanceLogs()->paginate(10),
        ]);
    }

    /**
     * Export resources to CSV
     */
    public function export()
    {
        $resources = Resource::all();
        
        $filename = "resources_" . date('Y-m-d_H-i-s') . ".csv";
        
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
        );

        $handle = fopen('php://output', 'w');
        fputcsv($handle, ['ID', 'Name', 'Category', 'Quantity', 'Available', 'Status', 'Condition', 'Location', 'Last Updated']);

        foreach ($resources as $resource) {
            fputcsv($handle, [
                $resource->id,
                $resource->name,
                $resource->category,
                $resource->quantity,
                $resource->available,
                $resource->status,
                $resource->condition,
                $resource->location,
                $resource->updated_at,
            ]);
        }

        fclose($handle);

        return response()->stream(function() {}, 200, $headers);
    }
}
