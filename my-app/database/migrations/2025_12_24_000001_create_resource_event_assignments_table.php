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
        Schema::create('resource_event_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resource_id')->constrained()->onDelete('cascade');
            $table->foreignId('event_id')->constrained('simulation_events')->onDelete('cascade');
            $table->integer('quantity_assigned')->default(1);
            $table->string('status')->default('Active'); // Active, Returned, Lost, Damaged
            $table->text('notes')->nullable();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('returned_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('returned_at')->nullable();
            $table->timestamps();
            
            $table->unique(['resource_id', 'event_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resource_event_assignments');
    }
};
