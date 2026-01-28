<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvaluationScore extends Model
{
    protected $fillable = [
        'participant_evaluation_id',
        'criterion_name',
        'criterion_description',
        'score',
        'max_score',
        'comment',
        'order',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'max_score' => 'decimal:2',
    ];

    public function participantEvaluation()
    {
        return $this->belongsTo(ParticipantEvaluation::class);
    }
}
