<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ResourceApiController;
use App\Models\Resource;

// Public API endpoints
Route::get('/resources', [ResourceApiController::class, 'index']);
Route::get('/resources/available', function () {
    $resources = Resource::where('status', 'Available')
        ->orWhere('status', 'Partially Assigned')
        ->where('available', '>', 0)
        ->get(['id', 'name', 'category', 'quantity', 'available', 'status', 'condition', 'location']);
    
    return response()->json(['resources' => $resources]);
});
Route::get('/simulation-events', [ResourceApiController::class, 'getEvents']);
Route::get('/simulation-events/completed-with-resources', [ResourceApiController::class, 'getCompletedEventResources']);
Route::get('/resources/{resource}/history', function (Resource $resource) {
    return response()->json([
        'resource' => $resource->load(['maintenanceLogs']),
        'history' => $resource->maintenanceLogs()->orderBy('created_at', 'desc')->get(),
    ]);
});
