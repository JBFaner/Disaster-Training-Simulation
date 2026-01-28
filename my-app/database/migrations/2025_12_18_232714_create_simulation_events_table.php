<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('simulation_events', function (Blueprint $table) {
            $table->id();
            
            // Section 1: Basic Event Information
            $table->string('title');
            $table->string('disaster_type');
            $table->text('description')->nullable();
            $table->string('event_category'); // Drill, Full-scale Exercise, Tabletop, Training Session
            $table->string('status')->default('draft'); // draft, published, ongoing, completed, archived, cancelled
            
            // Section 2: Event Schedule
            $table->date('event_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('is_recurring')->default(false);
            $table->string('recurrence_pattern')->nullable(); // daily, weekly, monthly, etc.
            
            // Section 3: Event Location
            $table->string('location')->nullable();
            $table->string('building')->nullable();
            $table->string('room_zone')->nullable();
            $table->text('location_notes')->nullable();
            $table->text('accessibility_notes')->nullable();
            $table->text('exits')->nullable();
            $table->text('hazard_zones')->nullable();
            $table->text('assembly_points')->nullable();
            $table->boolean('is_high_risk_location')->default(false);
            
            // Section 4: Scenario Assignment
            $table->foreignId('scenario_id')->nullable()->constrained('scenarios')->onDelete('set null');
            $table->boolean('scenario_is_required')->default(true);
            
            // Section 5: Trainers & Facilitators (stored as JSON for now, can be normalized later)
            $table->json('facilitators')->nullable(); // [{name, role, contact, role_type}]
            
            // Section 6: Participant Settings
            $table->json('allowed_participant_types')->nullable(); // ['staff', 'volunteers', 'students', 'responders']
            $table->unsignedInteger('max_participants')->nullable();
            $table->boolean('self_registration_enabled')->default(true);
            $table->boolean('qr_code_enabled')->default(false);
            $table->string('attendance_code')->nullable();
            
            // Section 7: Resource & Equipment Planning (stored as JSON, can be normalized later)
            $table->json('reserved_resources')->nullable(); // [{resource_id, quantity, status}]
            
            // Section 8: Safety & Compliance
            $table->text('safety_guidelines')->nullable();
            $table->text('hazard_warnings')->nullable();
            $table->text('required_ppe')->nullable();
            
            // Section 9: Event Workflow Configuration
            $table->json('event_phases')->nullable(); // [{phase_name, start_time, instructions}]
            $table->json('inject_triggers')->nullable(); // [{inject_id, trigger_time}]
            $table->text('facilitator_instructions')->nullable();
            
            // Section 10: Notifications
            $table->boolean('email_notifications_enabled')->default(true);
            $table->boolean('sms_notifications_enabled')->default(false);
            $table->json('notification_schedule')->nullable(); // [{type, timing, template}]
            
            // Metadata
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('published_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('simulation_events');
    }
};
