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
        Schema::create('event_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('simulation_event_id')->nullable();
            // Note: We're NOT adding the foreign key constraint here because simulation_events
            // table is created later in migrations. Add it in a separate migration after.
            $table->string('status')->default('pending'); // pending, approved, rejected, cancelled
            $table->text('rejection_reason')->nullable();
            $table->timestamp('registered_at')->useCurrent();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Ensure a user can only register once per event
            $table->unique(['user_id', 'simulation_event_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_registrations');
    }
};



