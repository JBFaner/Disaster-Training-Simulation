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
        Schema::create('training_modules', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('difficulty')->default('Beginner'); // Beginner / Intermediate / Advanced
            $table->string('category')->nullable(); // Earthquake, Fire, Flood, etc.
            $table->string('status')->default('draft'); // draft / published / unpublished / archived / deprecated
            $table->string('visibility')->default('all'); // all / group / staff_only
            $table->foreignId('owner_id')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_modules');
    }
};




