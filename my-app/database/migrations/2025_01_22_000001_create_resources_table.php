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
        if (Schema::hasTable('resources')) {
            return;
        }
        
        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category'); // PPE, Fire Equipment, Medical, Communication, Vehicles, Tools, etc.
            $table->text('description')->nullable();
            $table->integer('quantity')->default(1);
            $table->integer('available')->default(1);
            $table->string('condition')->default('Good'); // New, Good, Needs Repair, Damaged
            $table->string('status')->default('Available'); // Available, In Use, Under Maintenance, Damaged, Missing, Reserved
            $table->string('location'); // Warehouse, depot, room number, vehicle storage
            $table->string('serial_number')->unique()->nullable();
            $table->string('image_url')->nullable();
            $table->foreignId('assigned_to_event_id')->nullable()->constrained('simulation_events')->onDelete('set null');
            $table->foreignId('assigned_handler_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('maintenance_status')->nullable(); // Scheduled, In Progress, Completed, Overdue
            $table->datetime('last_maintenance_date')->nullable();
            $table->datetime('last_inspection_date')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resources');
    }
};
