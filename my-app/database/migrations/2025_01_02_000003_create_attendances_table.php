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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_registration_id')->constrained('event_registrations')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('simulation_event_id')->nullable();
            // Note: Not adding foreign key constraint here because simulation_events table doesn't exist yet
            $table->string('check_in_method')->nullable(); // manual, qr_code, attendance_code, auto
            $table->string('status')->default('present'); // present, late, absent, excused, completed
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamp('checked_out_at')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_locked')->default(false); // Lock attendance after event ends
            $table->foreignId('marked_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Ensure one attendance record per registration
            $table->unique('event_registration_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};



