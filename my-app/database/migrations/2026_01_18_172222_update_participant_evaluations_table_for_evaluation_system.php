<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('participant_evaluations', function (Blueprint $table) {
            // Add evaluation_id column if it doesn't exist
            if (!Schema::hasColumn('participant_evaluations', 'evaluation_id')) {
                $table->foreignId('evaluation_id')->nullable()->after('id')->constrained('evaluations')->onDelete('cascade');
            }
            
            // Add user_id column if it doesn't exist (we'll keep participant_id for now and add user_id)
            if (!Schema::hasColumn('participant_evaluations', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('evaluation_id')->constrained('users')->onDelete('cascade');
            }
            
            // Add evaluated_by column if it doesn't exist
            if (!Schema::hasColumn('participant_evaluations', 'evaluated_by')) {
                $table->foreignId('evaluated_by')->nullable()->after('user_id')->constrained('users')->onDelete('set null');
            }
            
            // Add missing columns that the model expects
            if (!Schema::hasColumn('participant_evaluations', 'weighted_score')) {
                $table->decimal('weighted_score', 5, 2)->nullable()->after('average_score');
            }
            
            if (!Schema::hasColumn('participant_evaluations', 'result')) {
                $table->string('result')->nullable()->after('weighted_score'); // passed, failed
            }
            
            if (!Schema::hasColumn('participant_evaluations', 'overall_feedback')) {
                $table->text('overall_feedback')->nullable()->after('result');
            }
            
            if (!Schema::hasColumn('participant_evaluations', 'is_eligible_for_certification')) {
                $table->boolean('is_eligible_for_certification')->default(false)->after('overall_feedback');
            }
            
            if (!Schema::hasColumn('participant_evaluations', 'submitted_at')) {
                $table->timestamp('submitted_at')->nullable()->after('is_eligible_for_certification');
            }
        });
        
        // Copy data from participant_id to user_id if user_id is null and participant_id exists
        if (Schema::hasColumn('participant_evaluations', 'participant_id')) {
            DB::statement('UPDATE participant_evaluations SET user_id = participant_id WHERE user_id IS NULL AND participant_id IS NOT NULL');
        }
        
        // Copy data from evaluator_id to evaluated_by if evaluated_by is null and evaluator_id exists
        if (Schema::hasColumn('participant_evaluations', 'evaluator_id')) {
            DB::statement('UPDATE participant_evaluations SET evaluated_by = evaluator_id WHERE evaluated_by IS NULL AND evaluator_id IS NOT NULL');
        }
        
        // Copy data from overall_remarks to overall_feedback if overall_feedback is null and overall_remarks exists
        if (Schema::hasColumn('participant_evaluations', 'overall_remarks')) {
            DB::statement('UPDATE participant_evaluations SET overall_feedback = overall_remarks WHERE overall_feedback IS NULL AND overall_remarks IS NOT NULL');
        }
        
        // Copy data from passed to result if result is null and passed exists
        if (Schema::hasColumn('participant_evaluations', 'passed')) {
            DB::statement('UPDATE participant_evaluations SET result = CASE WHEN passed = 1 THEN "passed" WHEN passed = 0 THEN "failed" ELSE NULL END WHERE result IS NULL AND passed IS NOT NULL');
        }
        
        // Add unique constraint if it doesn't exist
        if (Schema::hasColumn('participant_evaluations', 'evaluation_id') && Schema::hasColumn('participant_evaluations', 'user_id')) {
            $indexes = DB::select("SHOW INDEXES FROM participant_evaluations WHERE Key_name = 'participant_evaluations_evaluation_id_user_id_unique'");
            if (empty($indexes)) {
                Schema::table('participant_evaluations', function (Blueprint $table) {
                    try {
                        $table->unique(['evaluation_id', 'user_id']);
                    } catch (\Exception $e) {
                        // Unique constraint might already exist, ignore
                    }
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participant_evaluations', function (Blueprint $table) {
            // Drop unique constraint if it exists
            try {
                $table->dropUnique(['participant_evaluations_evaluation_id_user_id_unique']);
            } catch (\Exception $e) {
                // Constraint might not exist or have different name
            }
            
            // Drop columns if they exist
            if (Schema::hasColumn('participant_evaluations', 'submitted_at')) {
                $table->dropColumn('submitted_at');
            }
            
            if (Schema::hasColumn('participant_evaluations', 'is_eligible_for_certification')) {
                $table->dropColumn('is_eligible_for_certification');
            }
            
            if (Schema::hasColumn('participant_evaluations', 'overall_feedback')) {
                $table->dropColumn('overall_feedback');
            }
            
            if (Schema::hasColumn('participant_evaluations', 'result')) {
                $table->dropColumn('result');
            }
            
            if (Schema::hasColumn('participant_evaluations', 'weighted_score')) {
                $table->dropColumn('weighted_score');
            }
            
            if (Schema::hasColumn('participant_evaluations', 'evaluated_by')) {
                $table->dropForeign(['evaluated_by']);
                $table->dropColumn('evaluated_by');
            }
            
            if (Schema::hasColumn('participant_evaluations', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
            
            if (Schema::hasColumn('participant_evaluations', 'evaluation_id')) {
                $table->dropForeign(['evaluation_id']);
                $table->dropColumn('evaluation_id');
            }
        });
    }
};
