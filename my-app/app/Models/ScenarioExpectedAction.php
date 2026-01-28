<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScenarioExpectedAction extends Model
{
    protected $fillable = [
        'scenario_id',
        'description',
        'category',
        'order',
    ];

    public function scenario()
    {
        return $this->belongsTo(Scenario::class);
    }
}
