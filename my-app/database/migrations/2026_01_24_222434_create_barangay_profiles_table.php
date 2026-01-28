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
        Schema::create('barangay_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('barangay_name');
            $table->string('municipality_city');
            $table->string('province');
            $table->text('barangay_address')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('email_address')->nullable();
            $table->integer('estimated_population')->nullable();
            $table->enum('area_classification', ['Urban', 'Rural', 'Coastal', 'Mountainous'])->nullable();
            $table->json('hazards')->nullable(); // Array of hazards
            $table->text('hazard_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barangay_profiles');
    }
};
