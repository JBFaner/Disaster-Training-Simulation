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
        Schema::create('lesson_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_lesson_id')->constrained('training_lessons')->cascadeOnDelete();
            $table->string('type'); // pdf, video, image, ppt, link
            $table->string('label')->nullable();
            $table->string('path')->nullable(); // storage path or URL
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_materials');
    }
};
