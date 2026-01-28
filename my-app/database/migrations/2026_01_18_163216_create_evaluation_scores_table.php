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
        if (Schema::hasTable('evaluation_scores')) {
            return;
        }
        
        Schema::create('evaluation_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('participant_evaluation_id')->constrained('participant_evaluations')->onDelete('cascade');
            $table->string('criterion_name'); // Name of the criterion from scenario
            $table->text('criterion_description')->nullable();
            $table->decimal('score', 5, 2); // Score given for this criterion
            $table->decimal('max_score', 5, 2)->default(10.00); // Maximum possible score
            $table->text('comment')->nullable(); // Optional comment for this criterion
            $table->integer('order')->default(0); // Order of criterion in evaluation
            $table->timestamps();

            // Index for faster lookups
            $table->index('participant_evaluation_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluation_scores');
    }
};
