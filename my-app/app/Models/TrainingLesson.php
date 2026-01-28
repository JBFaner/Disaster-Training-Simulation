<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingLesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'training_module_id',
        'title',
        'description',
        'order',
        'is_required',
        'objectives',
    ];

    public function module()
    {
        return $this->belongsTo(TrainingModule::class, 'training_module_id');
    }

    public function materials()
    {
        return $this->hasMany(LessonMaterial::class, 'training_lesson_id');
    }

    public function completions()
    {
        return $this->hasMany(LessonCompletion::class, 'training_lesson_id');
    }
}


