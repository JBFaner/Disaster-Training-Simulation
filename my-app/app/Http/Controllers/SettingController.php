<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Get current auto-approval setting
     */
    public function getAutoApproval()
    {
        $enabled = Setting::get('event_auto_approval_enabled', false);
        return response()->json(['enabled' => $enabled]);
    }

    /**
     * Toggle auto-approval setting
     */
    public function toggleAutoApproval(Request $request)
    {
        $enabled = $request->boolean('enabled');
        
        Setting::set('event_auto_approval_enabled', $enabled ? '1' : '0', 'boolean');
        
        return response()->json([
            'success' => true,
            'enabled' => $enabled,
            'message' => $enabled 
                ? 'Auto-approval enabled for all events' 
                : 'Manual approval required for all events'
        ]);
    }
}
