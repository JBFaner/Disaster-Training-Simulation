<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Evaluation;
use App\Models\SimulationEvent;
use App\Models\User;
use App\Models\Scenario;
use App\Models\EventRegistration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EvaluationModuleTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'LGU_ADMIN']);
    }

    public function test_evaluation_dashboard_shows_published_ongoing_and_completed_events()
    {
        $publishedEvent = SimulationEvent::factory()->create(['status' => 'published', 'title' => 'Published Event']);
        $ongoingEvent = SimulationEvent::factory()->create(['status' => 'ongoing', 'title' => 'Ongoing Event']);
        $completedEvent = SimulationEvent::factory()->create(['status' => 'completed', 'title' => 'Completed Event']);
        $draftEvent = SimulationEvent::factory()->create(['status' => 'draft', 'title' => 'Draft Event']);

        $response = $this->actingAs($this->admin)->get(route('evaluations.index'));

        $response->assertStatus(200);
        $response->assertSee('Published Event');
        $response->assertSee('Ongoing Event');
        $response->assertSee('Completed Event');
        $response->assertDontSee('Draft Event');
    }

    public function test_evaluation_dashboard_search_functionality()
    {
        SimulationEvent::factory()->create(['status' => 'completed', 'title' => 'Searchable Event']);
        SimulationEvent::factory()->create(['status' => 'completed', 'title' => 'Other Event']);

        $response = $this->actingAs($this->admin)->get(route('evaluations.index', ['search' => 'Searchable']));

        $response->assertStatus(200);
        $response->assertSee('Searchable Event');
        $response->assertDontSee('Other Event');
    }

    public function test_evaluation_dashboard_status_filter_functionality()
    {
        $event1 = SimulationEvent::factory()->create(['status' => 'completed', 'title' => 'Event with Evaluation']);
        $event2 = SimulationEvent::factory()->create(['status' => 'completed', 'title' => 'Event without Evaluation']);

        Evaluation::create([
            'simulation_event_id' => $event1->id,
            'status' => 'in_progress',
            'pass_threshold' => 70,
            'created_by' => $this->admin->id
        ]);

        $response = $this->actingAs($this->admin)->get(route('evaluations.index', ['status' => 'in_progress']));

        $response->assertStatus(200);
        $response->assertSee('Event with Evaluation');
        $response->assertDontSee('Event without Evaluation');
    }

    public function test_evaluation_show_lists_all_approved_participants()
    {
        $event = SimulationEvent::factory()->create(['status' => 'completed']);
        $participant1 = User::factory()->create(['role' => 'PARTICIPANT', 'name' => 'Present Part']);
        $participant2 = User::factory()->create(['role' => 'PARTICIPANT', 'name' => 'Absent Part']);

        EventRegistration::create([
            'simulation_event_id' => $event->id,
            'user_id' => $participant1->id,
            'status' => 'approved'
        ]);

        EventRegistration::create([
            'simulation_event_id' => $event->id,
            'user_id' => $participant2->id,
            'status' => 'approved'
        ]);

        Attendance::create([
            'simulation_event_id' => $event->id,
            'user_id' => $participant1->id,
            'status' => 'present',
            'event_registration_id' => EventRegistration::where('user_id', $participant1->id)->first()->id
        ]);

        $response = $this->actingAs($this->admin)->get(route('evaluations.show', $event));

        $response->assertStatus(200);
        $response->assertSee('Present Part');
        $response->assertSee('Absent Part');
    }
}
