<?php

namespace App\Http\Controllers\Api;

use App\Models\Resource;
use App\Models\SimulationEvent;
use App\Models\ResourceEventAssignment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ResourceApiController
{
    /**
     * Get all resources with filtering
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Resource::query();

            if ($request->has('search')) {
                $query->where('name', 'like', "%{$request->search}%")
                      ->orWhere('serial_number', 'like', "%{$request->search}%");
            }

            if ($request->has('category') && $request->category !== 'all') {
                $query->where('category', $request->category);
            }

            if ($request->has('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            if ($request->has('condition') && $request->condition !== 'all') {
                $query->where('condition', $request->condition);
            }

            $resources = $query->get();

            return response()->json([
                'resources' => $resources,
                'stats' => [
                    'total' => Resource::count(),
                    'available' => Resource::where('status', 'Available')->count(),
                    'partiallyAssigned' => Resource::where('status', 'Partially Assigned')->count(),
                    'fullyAssigned' => Resource::where('status', 'Fully Assigned')->count(),
                    'needsRepair' => Resource::where('condition', 'Needs Repair')->orWhere('status', 'Damaged')->count(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'resources' => [],
                'stats' => [
                    'total' => 0,
                    'available' => 0,
                    'partiallyAssigned' => 0,
                    'fullyAssigned' => 0,
                    'needsRepair' => 0,
                ],
            ], 500);
        }
    }

    /**
     * Get all simulation events for assignment
     */
    public function getEvents(): JsonResponse
    {
        try {
            $events = SimulationEvent::where('status', 'published')
                ->orderBy('event_date', 'desc')
                ->get(['id', 'title', 'event_date']);

            return response()->json(['events' => $events]);
        } catch (\Exception $e) {
            return response()->json(['events' => [], 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get resource history/logs
     */
    public function getHistory(Resource $resource): JsonResponse
    {
        return response()->json([
            'resource' => $resource->load(['maintenanceLogs']),
            'history' => $resource->maintenanceLogs()->orderBy('created_at', 'desc')->get(),
        ]);
    }

    /**
     * Get resources from completed events that need to be returned
     */
    public function getCompletedEventResources(): JsonResponse
    {
        try {
            // Get completed events with their resource assignments
            $completedEvents = SimulationEvent::where('status', 'completed')
                ->with(['resources' => function ($query) {
                    $query->wherePivot('quantity_assigned', '>', 0)
                          ->wherePivot('status', '!=', 'Returned');
                }])
                ->orderBy('event_date', 'desc')
                ->get();

            // Also get active resource assignments for completed events
            $activeAssignments = ResourceEventAssignment::where('status', 'Active')
                ->whereHas('event', function ($query) {
                    $query->where('status', 'completed');
                })
                ->with(['resource', 'event'])
                ->get();

            // Format the response
            $eventsWithResources = $completedEvents->filter(function ($event) use ($activeAssignments) {
                // Check if event has resources via pivot or via assignments
                $hasResources = $event->resources->isNotEmpty();
                $hasAssignments = $activeAssignments->where('event_id', $event->id)->isNotEmpty();
                return $hasResources || $hasAssignments;
            })->map(function ($event) use ($activeAssignments) {
                $eventAssignments = $activeAssignments->where('event_id', $event->id);
                
                $resources = collect();
                
                // Add resources from pivot
                foreach ($event->resources as $resource) {
                    $resources->push([
                        'id' => $resource->id,
                        'name' => $resource->name,
                        'category' => $resource->category,
                        'serial_number' => $resource->serial_number,
                        'quantity_assigned' => $resource->pivot->quantity_assigned,
                        'status' => $resource->pivot->status,
                        'condition' => $resource->condition,
                    ]);
                }
                
                // Add resources from assignments if not already added
                foreach ($eventAssignments as $assignment) {
                    if (!$resources->where('id', $assignment->resource_id)->first()) {
                        $resources->push([
                            'id' => $assignment->resource->id,
                            'name' => $assignment->resource->name,
                            'category' => $assignment->resource->category,
                            'serial_number' => $assignment->resource->serial_number,
                            'quantity_assigned' => $assignment->quantity_assigned,
                            'status' => $assignment->status,
                            'condition' => $assignment->resource->condition,
                            'assignment_id' => $assignment->id,
                        ]);
                    }
                }
                
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'event_date' => $event->event_date,
                    'completed_at' => $event->completed_at,
                    'resources' => $resources->values(),
                ];
            })->values();

            return response()->json([
                'events' => $eventsWithResources,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'events' => [],
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
