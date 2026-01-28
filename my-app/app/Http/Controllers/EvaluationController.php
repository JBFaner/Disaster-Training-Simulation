<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\ParticipantEvaluation;
use App\Models\EvaluationScore;
use App\Models\SimulationEvent;
use App\Models\Attendance;
use App\Models\Scenario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EvaluationController extends Controller
{
    /**
     * Display evaluation dashboard with list of completed events
     */
    public function index(Request $request)
    {
        $this->authorizeEvaluationAccess();

        $query = SimulationEvent::whereIn('status', ['published', 'ongoing', 'completed'])
            ->with(['scenario', 'evaluation']);

        // Search by event title
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('title', 'like', "%{$search}%");
        }

        // Filter by evaluation status
        if ($request->has('status') && $request->status) {
            $statusFilter = $request->status;
            if ($statusFilter === 'not_started') {
                $query->whereDoesntHave('evaluation');
            } else {
                $query->whereHas('evaluation', function ($q) use ($statusFilter) {
                    $q->where('status', $statusFilter);
                });
            }
        }

        $events = $query->orderByDesc('event_date')
            ->orderByDesc('start_time')
            ->get()
            ->map(function ($event) {
                // Get attendance count
                $attendanceCount = Attendance::where('simulation_event_id', $event->id)
                    ->where('status', 'present')
                    ->count();

                // Get evaluation status
                $evaluation = $event->evaluation;
                $evaluationStatus = 'not_started';
                if ($evaluation) {
                    $evaluationStatus = $evaluation->status;
                }

                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'event_date' => $event->event_date,
                    'scenario_name' => $event->scenario ? $event->scenario->title : 'N/A',
                    'participant_count' => $attendanceCount,
                    'evaluation_status' => $evaluationStatus,
                    'evaluation' => $evaluation,
                    'status' => $event->status,
                ];
            });

        return view('app', [
            'section' => 'evaluation_dashboard',
            'events' => $events,
            'filters' => [
                'search' => $request->search,
                'status' => $request->status,
            ],
        ]);
    }

    /**
     * Show evaluation for a specific event
     */
    public function show(SimulationEvent $simulationEvent)
    {
        $this->authorizeEvaluationAccess();

        // Ensure event is in a relevant status
        if (!in_array($simulationEvent->status, ['published', 'ongoing', 'completed'])) {
            return redirect()->route('evaluations.index')
                ->with('status', 'Evaluation can only be viewed for published, ongoing, or completed events.');
        }

        // Get or create evaluation
        $evaluation = Evaluation::firstOrCreate(
            ['simulation_event_id' => $simulationEvent->id],
            [
                'status' => 'not_started',
                'pass_threshold' => 70.00,
                'created_by' => Auth::id(),
            ]
        );

        // Get scenario criteria
        $scenario = $simulationEvent->scenario;
        $criteria = [];
        if ($scenario && $scenario->criteria) {
            $criteria = is_array($scenario->criteria) ? $scenario->criteria : json_decode($scenario->criteria, true);
        }

        // Get all approved registrations for this event
        $registrations = $simulationEvent->registrations()
            ->where('status', 'approved')
            ->with(['user', 'attendance'])
            ->get();

        // Collect attendances from registrations for compatibility with existing React logic
        $attendances = $registrations->map(function ($reg) {
            return $reg->attendance ?: [
                'id' => 'reg-' . $reg->id, // Mock ID for non-attendance records
                'user_id' => $reg->user_id,
                'simulation_event_id' => $reg->simulation_event_id,
                'status' => 'not_marked',
                'user' => $reg->user,
            ];
        });

        // Get participant evaluations
        $participantEvaluations = ParticipantEvaluation::where('evaluation_id', $evaluation->id)
            ->with(['user', 'scores'])
            ->get()
            ->keyBy('user_id');

        return view('app', [
            'section' => 'evaluation_participants',
            'event' => $simulationEvent,
            'evaluation' => $evaluation,
            'criteria' => $criteria,
            'attendances' => $attendances,
            'participantEvaluations' => $participantEvaluations,
        ]);
    }

    /**
     * Show evaluation form for a specific participant
     */
    public function evaluate(SimulationEvent $simulationEvent, $userId)
    {
        $this->authorizeEvaluationAccess();

        $evaluation = Evaluation::where('simulation_event_id', $simulationEvent->id)->firstOrFail();

        if ($evaluation->isLocked()) {
            return redirect()->route('evaluations.show', $simulationEvent)
                ->with('status', 'Evaluation is locked and cannot be modified.');
        }

        $user = \App\Models\User::findOrFail($userId);
        $attendance = Attendance::where('simulation_event_id', $simulationEvent->id)
            ->where('user_id', $userId)
            ->whereIn('status', ['present', 'completed'])
            ->first();

        if (!$attendance) {
            return redirect()->route('evaluations.show', $simulationEvent)
                ->with('status', 'Cannot evaluate: Participant must be marked as present first.');
        }

        $scenario = $simulationEvent->scenario;
        if (!$scenario || !$scenario->criteria) {
            return redirect()->route('evaluations.show', $simulationEvent)
                ->with('status', 'Cannot evaluate: Scenario must have criteria defined.');
        }

        $criteria = is_array($scenario->criteria) ? $scenario->criteria : json_decode($scenario->criteria, true);

        $participantEvaluation = ParticipantEvaluation::firstOrCreate(
            [
                'evaluation_id' => $evaluation->id,
                'user_id' => $userId,
            ],
            [
                'attendance_id' => $attendance->id,
                'status' => 'draft',
                'evaluated_by' => Auth::id(),
            ]
        );

        // Get existing scores as array for JSON serialization
        $scores = $participantEvaluation->scores->map(function ($score) {
            return [
                'criterion_name' => $score->criterion_name,
                'score' => $score->score,
                'comment' => $score->comment,
            ];
        });

        return view('app', [
            'section' => 'evaluation_form',
            'event' => $simulationEvent,
            'evaluation' => $evaluation,
            'user' => $user,
            'attendance' => $attendance,
            'participantEvaluation' => $participantEvaluation,
            'criteria' => $criteria,
            'scores' => $scores->values()->all(),
        ]);
    }

    /**
     * Store or update participant evaluation
     */
    public function storeEvaluation(Request $request, SimulationEvent $simulationEvent, $userId)
    {
        $this->authorizeEvaluationAccess();

        $evaluation = Evaluation::where('simulation_event_id', $simulationEvent->id)->firstOrFail();

        if ($evaluation->isLocked()) {
            return back()->with('status', 'Evaluation is locked and cannot be modified.');
        }

        $user = \App\Models\User::findOrFail($userId);
        $attendance = Attendance::where('simulation_event_id', $simulationEvent->id)
            ->where('user_id', $userId)
            ->whereIn('status', ['present', 'completed'])
            ->first();

        if (!$attendance) {
            return redirect()->route('evaluations.show', $simulationEvent)
                ->with('status', 'Cannot evaluate: Participant must be marked as present first.');
        }

        $scenario = $simulationEvent->scenario;
        if (!$scenario || !$scenario->criteria) {
            return back()->with('status', 'Cannot evaluate: Scenario must have criteria defined.');
        }

        $criteria = is_array($scenario->criteria) ? $scenario->criteria : json_decode($scenario->criteria, true);

        $data = $request->validate([
            'scores' => ['required', 'array'],
            'scores.*.score' => ['required', 'numeric', 'min:0'],
            'scores.*.comment' => ['nullable', 'string', 'max:1000'],
            'overall_feedback' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', 'string', 'in:draft,submitted'],
        ]);

        DB::transaction(function () use ($evaluation, $user, $attendance, $criteria, $data) {
            $participantEvaluation = ParticipantEvaluation::updateOrCreate(
                [
                    'evaluation_id' => $evaluation->id,
                    'user_id' => $user->id,
                ],
                [
                    'attendance_id' => $attendance->id,
                    'status' => $data['status'],
                    'overall_feedback' => $data['overall_feedback'] ?? null,
                    'evaluated_by' => Auth::id(),
                    'submitted_at' => $data['status'] === 'submitted' ? now() : null,
                ]
            );

            // Delete existing scores
            $participantEvaluation->scores()->delete();

            // Create new scores
            $order = 0;
            foreach ($criteria as $criterion) {
                $criterionName = is_array($criterion) ? $criterion : $criterion;
                $scoreData = $data['scores'][$criterionName] ?? null;

                if ($scoreData) {
                    EvaluationScore::create([
                        'participant_evaluation_id' => $participantEvaluation->id,
                        'criterion_name' => $criterionName,
                        'criterion_description' => null,
                        'score' => $scoreData['score'],
                        'max_score' => 10.00, // Default max score, can be customized
                        'comment' => $scoreData['comment'] ?? null,
                        'order' => $order++,
                    ]);
                }
            }

            // Calculate scores
            $participantEvaluation->calculateScores();

            // Update evaluation status
            if ($evaluation->status === 'not_started') {
                $evaluation->update([
                    'status' => 'in_progress',
                    'started_at' => now(),
                    'updated_by' => Auth::id(),
                ]);
            }
        });

        $message = $data['status'] === 'submitted' 
            ? 'Evaluation submitted successfully.' 
            : 'Evaluation saved as draft.';

        return redirect()->route('evaluations.show', $simulationEvent)
            ->with('status', $message);
    }

    /**
     * Update evaluation status
     */
    public function updateStatus(Request $request, Evaluation $evaluation)
    {
        $this->authorizeEvaluationAccess();

        $data = $request->validate([
            'status' => ['required', 'string', 'in:not_started,in_progress,completed'],
        ]);

        if ($evaluation->isLocked()) {
            return back()->with('status', 'Evaluation is locked and cannot be modified.');
        }

        $updateData = [
            'status' => $data['status'],
            'updated_by' => Auth::id(),
        ];

        if ($data['status'] === 'completed' && !$evaluation->completed_at) {
            $updateData['completed_at'] = now();
        }

        $evaluation->update($updateData);

        return back()->with('status', 'Evaluation status updated successfully.');
    }

    /**
     * Lock evaluation
     */
    public function lock(Evaluation $evaluation)
    {
        $this->authorizeEvaluationAccess();

        if ($evaluation->isLocked()) {
            return back()->with('status', 'Evaluation is already locked.');
        }

        $evaluation->update([
            'status' => 'locked',
            'locked_at' => now(),
            'updated_by' => Auth::id(),
        ]);

        return back()->with('status', 'Evaluation locked successfully. Scores are now read-only.');
    }

    /**
     * Show evaluation summary
     */
    public function summary(SimulationEvent $simulationEvent)
    {
        $this->authorizeEvaluationAccess();

        $evaluation = Evaluation::where('simulation_event_id', $simulationEvent->id)->firstOrFail();

        $participantEvaluations = ParticipantEvaluation::where('evaluation_id', $evaluation->id)
            ->with(['user', 'scores'])
            ->get();

        $scenario = $simulationEvent->scenario;
        $criteria = [];
        if ($scenario && $scenario->criteria) {
            $criteria = is_array($scenario->criteria) ? $scenario->criteria : json_decode($scenario->criteria, true);
        }

        // Calculate statistics
        $totalParticipants = $participantEvaluations->count();
        $passedCount = $participantEvaluations->where('result', 'passed')->count();
        $failedCount = $participantEvaluations->where('result', 'failed')->count();

        // Calculate average scores per criterion
        $criterionAverages = [];
        foreach ($criteria as $criterion) {
            $criterionName = is_array($criterion) ? $criterion : $criterion;
            $scores = [];
            foreach ($participantEvaluations as $pe) {
                $score = $pe->scores->where('criterion_name', $criterionName)->first();
                if ($score) {
                    $scores[] = $score->score;
                }
            }
            $criterionAverages[$criterionName] = count($scores) > 0 
                ? round(array_sum($scores) / count($scores), 2) 
                : 0;
        }

        $overallAverage = $participantEvaluations->avg('average_score') ?? 0;

        return view('app', [
            'section' => 'evaluation_summary',
            'event' => $simulationEvent,
            'evaluation' => $evaluation,
            'participantEvaluations' => $participantEvaluations->values()->all(),
            'criteria' => $criteria,
            'criterionAverages' => $criterionAverages,
            'totalParticipants' => $totalParticipants,
            'passedCount' => $passedCount,
            'failedCount' => $failedCount,
            'overallAverage' => $overallAverage,
        ]);
    }

    /**
     * Export evaluation summary
     */
    public function export(SimulationEvent $simulationEvent, $format = 'csv')
    {
        $this->authorizeEvaluationAccess();

        $evaluation = Evaluation::where('simulation_event_id', $simulationEvent->id)->firstOrFail();

        $participantEvaluations = ParticipantEvaluation::where('evaluation_id', $evaluation->id)
            ->with(['user', 'scores'])
            ->get();

        if ($format === 'csv') {
            return $this->exportCsv($simulationEvent, $evaluation, $participantEvaluations);
        }

        // PDF export can be added later
        return back()->with('status', 'PDF export not yet implemented.');
    }

    protected function exportCsv($simulationEvent, $evaluation, $participantEvaluations)
    {
        $filename = 'evaluation_' . $simulationEvent->id . '_' . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($simulationEvent, $evaluation, $participantEvaluations) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, ['Participant Name', 'Total Score', 'Average Score', 'Result', 'Eligible for Certification']);
            
            // Data rows
            foreach ($participantEvaluations as $pe) {
                fputcsv($file, [
                    $pe->user->name,
                    $pe->total_score ?? 0,
                    $pe->average_score ?? 0,
                    $pe->result ?? 'N/A',
                    $pe->is_eligible_for_certification ? 'Yes' : 'No',
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    protected function authorizeEvaluationAccess(): void
    {
        $user = Auth::user();
        if (!$user) {
            abort(403);
        }
        if (!in_array($user->role, ['LGU_ADMIN', 'LGU_TRAINER'], true)) {
            abort(403);
        }
    }
}
