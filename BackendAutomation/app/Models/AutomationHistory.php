<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutomationHistory extends Model
{
     protected $fillable = [
        'user_id',
        'duration',
        'status',
        'total_videos',
        'results',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'results' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function videos()
    {
        return $this->hasMany(Video::class);
    }
}
