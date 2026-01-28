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
        Schema::create('scenarios', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('short_description')->nullable();
            $table->string('disaster_type'); // Earthquake, Fire, Flood, etc.
            $table->string('difficulty')->default('Basic'); // Basic / Intermediate / Advanced
            $table->string('intended_participants')->nullable();
            $table->text('safety_notes')->nullable();
            $table->string('incident_time')->nullable(); // day / night
            $table->string('weather')->nullable(); // sunny / rainy / stormy / windy
            $table->string('location_type')->nullable(); // building / field / etc.
            $table->unsignedInteger('casualty_count')->default(0);
            $table->text('learning_objectives')->nullable();
            $table->text('target_competencies')->nullable();
            $table->foreignId('training_module_id')->nullable()->constrained('training_modules')->nullOnDelete();
            $table->boolean('is_required_for_module')->default(false);
            $table->string('status')->default('draft'); // draft / published / archived
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scenarios');
    }
};
