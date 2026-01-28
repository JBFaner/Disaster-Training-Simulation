<?php

namespace App\Http\Controllers;

use App\Models\Scenario;
use App\Models\ScenarioInject;
use App\Models\ScenarioExpectedAction;
use App\Models\TrainingModule;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ScenarioController extends Controller
{
    public function index()
    {
        $scenarios = Scenario::with('trainingModule')
            ->orderByDesc('updated_at')
            ->get();

        return view('app', [
            'section' => 'scenario',
            'scenarios' => $scenarios,
        ]);
    }

    public function create()
    {
        $this->authorizeScenarioWrite();

        return view('app', [
            'section' => 'scenario_create',
            // Only published training modules can be linked to a scenario
            'modules' => TrainingModule::where('status', 'published')->orderBy('title')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeScenarioWrite();

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'short_description' => ['nullable', 'string'],
            'affected_area' => ['nullable', 'string', 'max:255'],
            'incident_time_text' => ['nullable', 'string', 'max:50'],
            'general_situation' => ['nullable', 'string'],
            'severity_level' => ['nullable', 'string', 'in:Low,Medium,High,Critical'],
            'difficulty' => ['required', 'string', 'max:50'],
            'intended_participants' => ['nullable', 'string', 'max:255'],
            'injured_victims_count' => ['nullable', 'integer', 'min:0'],
            'trapped_persons_count' => ['nullable', 'integer', 'min:0'],
            'infrastructure_damage' => ['nullable', 'string'],
            'communication_status' => ['nullable', 'string', 'in:working,unstable,down'],
            'criteria' => ['required', 'array', 'min:1'],
            'criteria.*' => ['required', 'string', 'max:500'],
            // Enforce published-only linkage (draft modules cannot be linked)
            'training_module_id' => ['required', Rule::exists('training_modules', 'id')->where('status', 'published')],
        ]);

        $module = TrainingModule::findOrFail($data['training_module_id']);
        $data['disaster_type'] = $module->category ?? 'Unknown';

        // Validate that training module has learning objectives
        if (!$module->learning_objectives || empty($module->learning_objectives)) {
            return redirect()->back()
                ->withErrors(['training_module_id' => 'The selected training module must have learning objectives.'])
                ->withInput();
        }

        // Filter out empty criteria and convert to JSON
        if (isset($data['criteria'])) {
            $data['criteria'] = array_values(array_filter($data['criteria']));
            if (empty($data['criteria'])) {
                return redirect()->back()
                    ->withErrors(['criteria' => 'At least one criterion is required.'])
                    ->withInput();
            }
        }

        $data['status'] = 'draft';
        $data['created_by'] = Auth::id();

        Scenario::create($data);

        return redirect()->route('scenarios.index')
            ->with('status', 'Scenario created successfully.');
    }

    public function show(Scenario $scenario)
    {
        $this->authorizeScenarioWrite();

        return view('app', [
            'section' => 'scenario_detail',
            'scenario' => $scenario->load(['trainingModule', 'injects', 'expectedActions']),
        ]);
    }

    public function edit(Scenario $scenario)
    {
        $this->authorizeScenarioWrite();

        return view('app', [
            'section' => 'scenario_edit',
            'scenario' => $scenario->load('trainingModule'),
            // Allow selecting only published modules. Also include currently-linked module (even if not published)
            // so the UI can display it, but saving will require selecting a published module.
            'modules' => TrainingModule::where('status', 'published')
                ->orWhere('id', $scenario->training_module_id)
                ->orderBy('title')
                ->get(),
        ]);
    }

    public function update(Request $request, Scenario $scenario)
    {
        $this->authorizeScenarioWrite();

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'short_description' => ['nullable', 'string'],
            'affected_area' => ['nullable', 'string', 'max:255'],
            'incident_time_text' => ['nullable', 'string', 'max:50'],
            'general_situation' => ['nullable', 'string'],
            'severity_level' => ['nullable', 'string', 'in:Low,Medium,High,Critical'],
            'difficulty' => ['required', 'string', 'max:50'],
            'intended_participants' => ['nullable', 'string', 'max:255'],
            'injured_victims_count' => ['nullable', 'integer', 'min:0'],
            'trapped_persons_count' => ['nullable', 'integer', 'min:0'],
            'infrastructure_damage' => ['nullable', 'string'],
            'communication_status' => ['nullable', 'string', 'in:working,unstable,down'],
            'criteria' => ['required', 'array', 'min:1'],
            'criteria.*' => ['required', 'string', 'max:500'],
            'training_module_id' => ['required', Rule::exists('training_modules', 'id')->where('status', 'published')],
        ]);

        $module = TrainingModule::findOrFail($data['training_module_id']);
        $data['disaster_type'] = $module->category ?? 'Unknown';

        // Validate that training module has learning objectives
        if (!$module->learning_objectives || empty($module->learning_objectives)) {
            return redirect()->back()
                ->withErrors(['training_module_id' => 'The selected training module must have learning objectives.'])
                ->withInput();
        }

        // Filter out empty criteria and convert to JSON
        if (isset($data['criteria'])) {
            $data['criteria'] = array_values(array_filter($data['criteria']));
            if (empty($data['criteria'])) {
                return redirect()->back()
                    ->withErrors(['criteria' => 'At least one criterion is required.'])
                    ->withInput();
            }
        }

        $data['updated_by'] = Auth::id();

        $scenario->update($data);

        return redirect()->route('scenarios.index')
            ->with('status', 'Scenario updated successfully.');
    }

    public function publish(Scenario $scenario)
    {
        $this->authorizeScenarioWrite();

        $scenario->update([
            'status' => 'published',
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('scenarios.index')
            ->with('status', 'Scenario published successfully.');
    }

    public function archive(Scenario $scenario)
    {
        $this->authorizeScenarioWrite();

        $scenario->update([
            'status' => 'archived',
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('scenarios.index')
            ->with('status', 'Scenario archived.');
    }

    public function destroy(Scenario $scenario)
    {
        $this->authorizeScenarioDelete();

        $scenario->delete();

        return redirect()->route('scenarios.index')
            ->with('status', 'Scenario deleted permanently.');
    }

    protected function authorizeScenarioWrite(): void
    {
        $user = Auth::user();
        if (! $user) abort(403);
        if (! in_array($user->role, ['LGU_ADMIN', 'LGU_TRAINER'], true)) abort(403);
    }

    public function storeInject(Request $request, Scenario $scenario)
    {
        $this->authorizeScenarioWrite();

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'trigger_time_text' => ['required', 'string', 'max:255'],
        ]);

        $scenario->injects()->create($data);

        return redirect()->route('scenarios.show', $scenario)
            ->with('status', 'Inject added successfully.');
    }

    public function destroyInject(Scenario $scenario, ScenarioInject $inject)
    {
        $this->authorizeScenarioWrite();

        // Ensure the inject belongs to the scenario
        if ($inject->scenario_id !== $scenario->id) {
            abort(404);
        }

        $inject->delete();

        return redirect()->route('scenarios.show', $scenario)
            ->with('status', 'Inject removed successfully.');
    }

    public function storeExpectedAction(Request $request, Scenario $scenario)
    {
        $this->authorizeScenarioWrite();

        $data = $request->validate([
            'description' => ['required', 'string'],
            'category' => ['nullable', 'string', 'max:255'],
            'order' => ['nullable', 'integer', 'min:0'],
        ]);

        if (!isset($data['order'])) {
            $data['order'] = $scenario->expectedActions()->max('order') + 1 ?? 0;
        }

        $scenario->expectedActions()->create($data);

        return redirect()->route('scenarios.show', $scenario)
            ->with('status', 'Expected action added successfully.');
    }

    public function destroyExpectedAction(Scenario $scenario, ScenarioExpectedAction $expectedAction)
    {
        $this->authorizeScenarioWrite();

        // Ensure the expected action belongs to the scenario
        if ($expectedAction->scenario_id !== $scenario->id) {
            abort(404);
        }

        $expectedAction->delete();

        return redirect()->route('scenarios.show', $scenario)
            ->with('status', 'Expected action removed successfully.');
    }

    /**
     * Generate scenario using AI based on user prompt
     */
    public function generateAiScenario(Request $request)
    {
        $this->authorizeScenarioWrite();

        $data = $request->validate([
            'prompt' => ['required', 'string', 'min:10', 'max:1000'],
            'disaster_type' => ['nullable', 'string', 'max:255'],
            'difficulty' => ['nullable', 'string', 'in:Basic,Intermediate,Advanced'],
        ]);

        try {
            $geminiService = new GeminiService();
            $scenarioData = $geminiService->generateScenarioFromPrompt(
                $data['prompt'],
                $data['disaster_type'] ?? null,
                $data['difficulty'] ?? 'Medium'
            );

            return response()->json([
                'success' => true,
                'data' => $scenarioData,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    protected function authorizeScenarioDelete(): void
    {
        $user = Auth::user();
        if (! $user) abort(403);
        if ($user->role !== 'LGU_ADMIN') abort(403);
    }
}
