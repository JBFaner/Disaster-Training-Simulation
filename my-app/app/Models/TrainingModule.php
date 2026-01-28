<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingModule extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'learning_objectives',
        'difficulty',
        'category',
        'status',
        'visibility',
        'owner_id',
    ];

    protected $casts = [
        'learning_objectives' => 'array',
    ];

    public function lessons()
    {
        return $this->hasMany(TrainingLesson::class)->orderBy('order');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}


