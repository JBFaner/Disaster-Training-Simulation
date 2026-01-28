<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\EventRegistration;
use App\Models\SimulationEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    /**
     * Display attendance for a specific event.
     */
    public function index(Request $request, SimulationEvent $simulationEvent)
    {
        $this->authorizeAttendanceAccess();

        $registrations = $simulationEvent->registrations()
            ->where('status', 'approved')
            ->with(['user', 'attendance'])
            ->orderBy('registered_at', 'desc')
            ->get();

        return view('app', [
            'section' => 'event_attendance',
            'event' => $simulationEvent,
            'registrations' => $registrations,
        ]);
    }

    /**
     * Mark or update attendance.
     */
    public function store(Request $request, EventRegistration $eventRegistration)
    {
        $this->authorizeAttendanceAccess();

        if ($eventRegistration->status !== 'approved') {
            return back()->with('status', 'Can only mark attendance for approved registrations.');
        }

        $data = $request->validate([
            'check_in_method' => ['required', 'string', 'in:manual,qr_code,attendance_code,auto'],
            'status' => ['required', 'string', 'in:present,late,absent,excused,completed'],
            'checked_in_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $attendance = $eventRegistration->attendance;

        if ($attendance) {
            // Update existing attendance
            if ($attendance->is_locked) {
                return back()->with('status', 'Attendance is locked and cannot be modified.');
            }

            $attendance->update([
                'check_in_method' => $data['check_in_method'],
                'status' => $data['status'],
                'checked_in_at' => isset($data['checked_in_at']) && $data['checked_in_at'] ? now()->parse($data['checked_in_at']) : now(),
                'notes' => $data['notes'] ?? null,
                'marked_by' => Auth::id(),
            ]);
        } else {
            // Create new attendance
            Attendance::create([
                'event_registration_id' => $eventRegistration->id,
                'user_id' => $eventRegistration->user_id,
                'simulation_event_id' => $eventRegistration->simulation_event_id,
                'check_in_method' => $data['check_in_method'],
                'status' => $data['status'],
                'checked_in_at' => isset($data['checked_in_at']) && $data['checked_in_at'] ? now()->parse($data['checked_in_at']) : now(),
                'notes' => $data['notes'] ?? null,
                'marked_by' => Auth::id(),
            ]);
        }

        return back()->with('status', 'Attendance marked successfully.');
    }

    /**
     * Update attendance status.
     */
    public function update(Request $request, Attendance $attendance)
    {
        $this->authorizeAttendanceAccess();

        if ($attendance->is_locked) {
            return back()->with('status', 'Attendance is locked and cannot be modified.');
        }

        $data = $request->validate([
            'status' => ['required', 'string', 'in:present,late,absent,excused,completed'],
            'checked_in_at' => ['nullable', 'date'],
            'checked_out_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $attendance->update([
            'status' => $data['status'],
            'checked_in_at' => isset($data['checked_in_at']) && $data['checked_in_at'] ? now()->parse($data['checked_in_at']) : $attendance->checked_in_at,
            'checked_out_at' => isset($data['checked_out_at']) && $data['checked_out_at'] ? now()->parse($data['checked_out_at']) : $attendance->checked_out_at,
            'notes' => $data['notes'] ?? null,
            'marked_by' => Auth::id(),
        ]);

        return back()->with('status', 'Attendance updated successfully.');
    }

    /**
     * Lock attendance after event ends.
     */
    public function lock(SimulationEvent $simulationEvent)
    {
        $this->authorizeAttendanceAccess();

        $simulationEvent->attendances()->update(['is_locked' => true]);

        return back()->with('status', 'Attendance records locked.');
    }

    /**
     * Export attendance report (CSV).
     */
    public function export(Request $request, SimulationEvent $simulationEvent)
    {
        $this->authorizeAttendanceAccess();

        $attendances = $simulationEvent->attendances()
            ->with(['user', 'eventRegistration'])
            ->orderBy('checked_in_at', 'desc')
            ->get();

        $filename = 'attendance_' . $simulationEvent->id . '_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($attendances) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Participant ID', 'Name', 'Email', 'Status', 'Check-in Method', 'Checked In At', 'Checked Out At', 'Notes']);

            foreach ($attendances as $attendance) {
                fputcsv($file, [
                    $attendance->user->participant_id ?? 'N/A',
                    $attendance->user->name,
                    $attendance->user->email,
                    $attendance->status,
                    $attendance->check_in_method ?? 'N/A',
                    $attendance->checked_in_at ? $attendance->checked_in_at->format('Y-m-d H:i:s') : 'N/A',
                    $attendance->checked_out_at ? $attendance->checked_out_at->format('Y-m-d H:i:s') : 'N/A',
                    $attendance->notes ?? '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Mark attendance as present via QR code scan.
     */
    public function markPresentByQR(Request $request, SimulationEvent $simulationEvent)
    {
        $this->authorizeAttendanceAccess();

        $data = $request->validate([
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'participant_id' => ['nullable', 'string'],
            'check_in_method' => ['required', 'string', 'in:qr_code'],
        ]);

        if (empty($data['user_id']) && empty($data['participant_id'])) {
            return response()->json([
                'success' => false,
                'message' => 'QR code missing user identifier',
            ], 400);
        }

        // Resolve user by id or participant_id
        $userId = $data['user_id'];
        if (!$userId && !empty($data['participant_id'])) {
            $user = \App\Models\User::where('participant_id', $data['participant_id'])->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Participant not found for this QR code',
                ], 404);
            }
            $userId = $user->id;
        }

        // Find the event registration
        $eventRegistration = $simulationEvent->registrations()
            ->where('user_id', $userId)
            ->where('status', 'approved')
            ->first();

        if (!$eventRegistration) {
            return response()->json([
                'success' => false,
                'message' => 'No approved registration found for this participant'
            ], 404);
        }

        // Check if attendance already exists
        $attendance = $eventRegistration->attendance;

        if ($attendance) {
            // Update existing attendance
            $attendance->update([
                'status' => 'present',
                'check_in_method' => $data['check_in_method'],
                'checked_in_at' => now(),
                'marked_by' => Auth::id(),
            ]);
        } else {
            // Create new attendance record
            Attendance::create([
                'event_registration_id' => $eventRegistration->id,
                'user_id' => $userId,
                'simulation_event_id' => $simulationEvent->id,
                'status' => 'present',
                'check_in_method' => $data['check_in_method'],
                'checked_in_at' => now(),
                'marked_by' => Auth::id(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Attendance marked as present successfully'
        ]);
    }

    /**
     * Authorize attendance access (Admin and Trainer only).
     */
    private function authorizeAttendanceAccess()
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['LGU_ADMIN', 'LGU_TRAINER'], true)) {
            abort(403, 'Unauthorized access.');
        }
    }
}



