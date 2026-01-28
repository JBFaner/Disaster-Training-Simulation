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
        if (Schema::hasTable('participant_evaluations')) {
            return;
        }
        
        Schema::create('participant_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_id')->constrained('evaluations')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('attendance_id')->nullable()->constrained('attendances')->onDelete('set null');
            $table->string('status')->default('draft'); // draft, submitted
            $table->decimal('total_score', 8, 2)->nullable();
            $table->decimal('average_score', 5, 2)->nullable();
            $table->decimal('weighted_score', 5, 2)->nullable();
            $table->string('result')->nullable(); // passed, failed
            $table->text('overall_feedback')->nullable();
            $table->boolean('is_eligible_for_certification')->default(false);
            $table->foreignId('evaluated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            // One evaluation per participant per event evaluation
            $table->unique(['evaluation_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participant_evaluations');
    }
};
