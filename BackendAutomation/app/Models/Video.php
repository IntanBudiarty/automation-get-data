<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
     protected $fillable = [
        'automation_history_id',
        'title',
        'channel',
        'url',
        'views',
        'scraped_at',
    ];

    public function history()
    {
        return $this->belongsTo(AutomationHistory::class, 'automation_history_id');
    }
}
