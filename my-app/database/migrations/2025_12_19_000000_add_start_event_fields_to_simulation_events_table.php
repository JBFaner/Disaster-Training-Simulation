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
        // Check if table exists (SQLite-safe)
        if (!Schema::hasTable('simulation_events')) {
            return;
        }

        // Check which columns need to be added (SQLite-safe)
        $needsActualStartTime = !Schema::hasColumn('simulation_events', 'actual_start_time');
        $needsStartedBy = !Schema::hasColumn('simulation_events', 'started_by');

        // Only modify table if there are columns to add
        if ($needsActualStartTime || $needsStartedBy) {
            Schema::table('simulation_events', function (Blueprint $table) use ($needsActualStartTime, $needsStartedBy) {
                // Remove 'after()' for SQLite compatibility (SQLite doesn't support column positioning)
                if ($needsActualStartTime) {
                    $table->dateTime('actual_start_time')->nullable();
                }
                
                if ($needsStartedBy) {
                    $table->unsignedBigInteger('started_by')->nullable();
                }
            });
        }

        // Add foreign key constraint separately (SQLite-safe handling)
        if ($needsStartedBy || Schema::hasColumn('simulation_events', 'started_by')) {
            try {
                Schema::table('simulation_events', function (Blueprint $table) {
                    // Check if foreign key doesn't already exist
                    $table->foreign('started_by')->references('id')->on('users')->onDelete('set null');
                });
            } catch (\Exception $e) {
                // Foreign key might already exist or not be supported in SQLite
                // Continue without throwing error
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Check if table exists (SQLite-safe)
        if (!Schema::hasTable('simulation_events')) {
            return;
        }

        Schema::table('simulation_events', function (Blueprint $table) {
            // Only drop foreign key if column exists
            if (Schema::hasColumn('simulation_events', 'started_by')) {
                try {
                    $table->dropForeignKey(['started_by']);
                } catch (\Exception $e) {
                    // Foreign key might not exist
                }
            }
            
            // Only drop columns if they exist
            $columnsToDrop = [];
            if (Schema::hasColumn('simulation_events', 'actual_start_time')) {
                $columnsToDrop[] = 'actual_start_time';
            }
            if (Schema::hasColumn('simulation_events', 'started_by')) {
                $columnsToDrop[] = 'started_by';
            }
            
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};

