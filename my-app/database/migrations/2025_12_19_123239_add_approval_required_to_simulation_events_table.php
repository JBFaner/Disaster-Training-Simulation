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
        Schema::table('simulation_events', function (Blueprint $table) {
            $table->boolean('approval_required')->default(true)->after('self_registration_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('simulation_events', function (Blueprint $table) {
            $table->dropColumn('approval_required');
        });
    }
};
