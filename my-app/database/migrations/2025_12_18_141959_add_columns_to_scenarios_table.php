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
        // These columns may already exist if the create migration already added them
        // Only add them if they don't exist yet
        Schema::table('scenarios', function (Blueprint $table) {
            if (!Schema::hasColumn('scenarios', 'title')) {
                $table->string('title');
            }
            if (!Schema::hasColumn('scenarios', 'short_description')) {
                $table->text('short_description')->nullable();
            }
            if (!Schema::hasColumn('scenarios', 'disaster_type')) {
                $table->string('disaster_type');
            }
            if (!Schema::hasColumn('scenarios', 'difficulty')) {
                $table->string('difficulty')->default('Basic');
            }
            if (!Schema::hasColumn('scenarios', 'intended_participants')) {
                $table->string('intended_participants')->nullable();
            }
            if (!Schema::hasColumn('scenarios', 'safety_notes')) {
                $table->text('safety_notes')->nullable();
            }
            if (!Schema::hasColumn('scenarios', 'incident_time')) {
                $table->string('incident_time')->nullable();
            }
            if (!Schema::hasColumn('scenarios', 'weather')) {
                $table->string('weather')->nullable();
            }
            if (!Schema::hasColumn('scenarios', 'location_type')) {
                $table->string('location_type')->nullable();
            }
            if (!Schema::hasColumn('scenarios', 'casualty_count')) {
                $table->unsignedInteger('casualty_count')->default(0);
            }
            if (!Schema::hasColumn('scenarios', 'learning_objectives')) {
                $table->text('learning_objectives')->nullable();
            }
            if (!Schema::hasColumn('scenarios', 'target_competencies')) {
                $table->text('target_competencies')->nullable();
            }
            if (!Schema::hasColumn('scenarios', 'training_module_id')) {
                $table->foreignId('training_module_id')->nullable()->constrained('training_modules')->nullOnDelete();
            }
            if (!Schema::hasColumn('scenarios', 'is_required_for_module')) {
                $table->boolean('is_required_for_module')->default(false);
            }
            if (!Schema::hasColumn('scenarios', 'status')) {
                $table->string('status')->default('draft');
            }
            if (!Schema::hasColumn('scenarios', 'created_by')) {
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('scenarios', 'updated_by')) {
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scenarios', function (Blueprint $table) {
            $table->dropConstrainedForeignId('training_module_id');
            $table->dropConstrainedForeignId('created_by');
            $table->dropConstrainedForeignId('updated_by');

            $table->dropColumn([
                'title',
                'short_description',
                'disaster_type',
                'difficulty',
                'intended_participants',
                'safety_notes',
                'incident_time',
                'weather',
                'location_type',
                'casualty_count',
                'learning_objectives',
                'target_competencies',
                'is_required_for_module',
                'status',
            ]);
        });
    }
};
