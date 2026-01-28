<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResourceMaintenanceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'resource_id',
        'action',
        'notes',
        'technician',
        'recorded_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the resource this log belongs to
     */
    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }

    /**
     * Get the user who recorded this log
     */
    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
