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
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('simulation_event_id')->constrained('simulation_events')->onDelete('cascade');
            $table->string('status')->default('not_started'); // not_started, in_progress, completed, locked
            $table->decimal('pass_threshold', 5, 2)->default(70.00); // Default 70% pass threshold
            $table->text('overall_notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('locked_at')->nullable();
            $table->timestamps();

            // One evaluation per event
            $table->unique('simulation_event_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};
