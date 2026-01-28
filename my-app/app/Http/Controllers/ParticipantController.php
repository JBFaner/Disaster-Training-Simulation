<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SimulationEvent;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ParticipantController extends Controller
{
    /**
     * Display a listing of participants.
     */
    public function index(Request $request)
    {
        $this->authorizeParticipantAccess();

        $query = User::where('role', 'PARTICIPANT')
            ->withCount(['eventRegistrations', 'attendances']);

        // Search by name or participant_id
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('participant_id', 'like', "%{$search}%");
            });
        }

        // Filter by role (participant type)
        if ($request->has('role_filter') && $request->role_filter) {
            // This would need a separate field or we can use a different approach
            // For now, we'll skip this filter
        }

        // Filter by status
        if ($request->has('status_filter') && $request->status_filter) {
            $query->where('status', $request->status_filter);
        }

        $participants = $query->orderBy('created_at', 'desc')->get();

        // Load events with registration counts for the tabs
        $events = SimulationEvent::with(['scenario'])
            ->withCount([
                'registrations',
                'registrations as approved_registrations_count' => function ($query) {
                    $query->where('status', 'approved');
                }
            ])
            // Show active lifecycle events for admin registration/attendance management
            ->whereIn('status', ['published', 'ongoing', 'completed'])
            ->orderByDesc('event_date')
            ->get();

        return view('app', [
            'section' => 'participants',
            'participants' => $participants,
            'events' => $events,
        ]);
    }

    /**
     * Show participant profile (read-only core info).
     */
    public function show(User $user)
    {
        $this->authorizeParticipantAccess();

        if ($user->role !== 'PARTICIPANT') {
            abort(404);
        }

        $user->load([
            'eventRegistrations.simulationEvent',
            'eventRegistrations.attendance',
            'attendances.simulationEvent',
        ]);

        return view('app', [
            'section' => 'participant_detail',
            'participant' => $user,
        ]);
    }

    /**
     * Update limited participant fields (role correction, status).
     */
    public function update(Request $request, User $user)
    {
        $this->authorizeParticipantAccess();

        if ($user->role !== 'PARTICIPANT') {
            abort(404);
        }

        $data = $request->validate([
            'status' => ['nullable', 'string', 'in:active,inactive'],
            'phone' => ['nullable', 'string', 'max:255'],
        ]);

        $user->update($data);

        return redirect()->route('participants.show', $user)
            ->with('status', 'Participant updated successfully.');
    }

    /**
     * Deactivate participant (soft delete).
     */
    public function deactivate(User $user)
    {
        $this->authorizeParticipantAccess();

        if ($user->role !== 'PARTICIPANT') {
            abort(404);
        }

        $user->update(['status' => 'inactive']);

        return redirect()->route('participants.index')
            ->with('status', 'Participant deactivated.');
    }

    /**
     * Reactivate participant.
     */
    public function reactivate(User $user)
    {
        $this->authorizeParticipantAccess();

        if ($user->role !== 'PARTICIPANT') {
            abort(404);
        }

        $user->update(['status' => 'active']);

        return redirect()->route('participants.index')
            ->with('status', 'Participant reactivated.');
    }

    /**
     * Export participant list (CSV).
     */
    public function export(Request $request)
    {
        $this->authorizeParticipantAccess();

        $participants = User::where('role', 'PARTICIPANT')
            ->withCount(['eventRegistrations', 'attendances'])
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'participants_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($participants) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Participant ID', 'Name', 'Email', 'Phone', 'Status', 'Registered At', 'Events Registered', 'Attendances']);

            foreach ($participants as $participant) {
                fputcsv($file, [
                    $participant->participant_id ?? 'N/A',
                    $participant->name,
                    $participant->email,
                    $participant->phone ?? 'N/A',
                    $participant->status,
                    $participant->registered_at ? $participant->registered_at->format('Y-m-d H:i:s') : 'N/A',
                    $participant->event_registrations_count,
                    $participant->attendances_count,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Participant self-service attendance view.
     */
    public function myAttendance()
    {
        $user = Auth::user();
        /** @var \App\Models\User|null $user */
        if (!$user || $user->role !== 'PARTICIPANT') {
            abort(403, 'Unauthorized access.');
        }

        $user->load(['attendances.simulationEvent']);

        return view('app', [
            'section' => 'my_attendance',
            'participant' => $user,
        ]);
    }

    /**
     * Generate unique participant ID.
     */
    private function generateParticipantId()
    {
        do {
            $id = 'PART-' . strtoupper(Str::random(8));
        } while (User::where('participant_id', $id)->exists());

        return $id;
    }

    /**
     * Authorize participant access (Admin and Trainer only).
     */
    private function authorizeParticipantAccess()
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['LGU_ADMIN', 'LGU_TRAINER'], true)) {
            abort(403, 'Unauthorized access.');
        }
    }
}

