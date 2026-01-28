<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_resource', function (Blueprint $table) {
            $table->id();
            $table->foreignId('simulation_event_id')->constrained('simulation_events')->onDelete('cascade');
            $table->foreignId('resource_id')->constrained('resources')->onDelete('cascade');
            $table->integer('quantity_needed')->default(1);
            $table->integer('quantity_assigned')->default(0);
            $table->string('status')->default('Planned'); // Planned, Assigned, In Use, Returned
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['simulation_event_id', 'resource_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_resource');
    }
};
