<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScenarioInject extends Model
{
    protected $fillable = [
        'scenario_id',
        'title',
        'description',
        'trigger_time_text',
    ];

    public function scenario()
    {
        return $this->belongsTo(Scenario::class);
    }
}
