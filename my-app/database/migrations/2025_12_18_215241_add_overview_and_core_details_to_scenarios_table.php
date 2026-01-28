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
        Schema::table('scenarios', function (Blueprint $table) {
            // Scenario Overview fields
            $table->string('affected_area')->nullable()->after('short_description');
            $table->string('incident_time_text')->nullable()->after('incident_time'); // e.g., "10:17 AM"
            $table->text('general_situation')->nullable()->after('incident_time_text');
            $table->string('severity_level')->nullable()->after('general_situation'); // Low, Medium, High, Critical

            // Core Scenario Details
            $table->unsignedInteger('injured_victims_count')->default(0)->after('casualty_count');
            $table->unsignedInteger('trapped_persons_count')->default(0)->after('injured_victims_count');
            $table->text('infrastructure_damage')->nullable()->after('trapped_persons_count');
            $table->string('communication_status')->nullable()->after('infrastructure_damage'); // working, unstable, down
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scenarios', function (Blueprint $table) {
            $table->dropColumn([
                'affected_area',
                'incident_time_text',
                'general_situation',
                'severity_level',
                'injured_victims_count',
                'trapped_persons_count',
                'infrastructure_damage',
                'communication_status',
            ]);
        });
    }
};
