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
        Schema::create('scenario_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scenario_id')->constrained('scenarios')->cascadeOnDelete();
            $table->unsignedInteger('order')->default(0);
            $table->string('trigger_type')->default('time'); // time / manual
            $table->unsignedInteger('trigger_offset_seconds')->nullable();
            $table->text('description');
            $table->text('instructor_prompt')->nullable();
            $table->text('expected_actions')->nullable();
            $table->boolean('is_escalation')->default(false);
            $table->boolean('is_end')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scenario_events');
    }
};
