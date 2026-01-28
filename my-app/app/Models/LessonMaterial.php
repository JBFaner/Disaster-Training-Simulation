<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'training_lesson_id',
        'type',
        'label',
        'path',
    ];

    public function lesson()
    {
        return $this->belongsTo(TrainingLesson::class, 'training_lesson_id');
    }
}




