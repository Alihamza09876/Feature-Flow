<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RoadmapStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'topic_id',
        'title',
        'description',
        'order',
        'completed'
    ];

    protected $casts = [
        'completed' => 'boolean'
    ];

    public function topic()
    {
        return $this->belongsTo(RoadmapTopic::class, 'topic_id');
    }
}
