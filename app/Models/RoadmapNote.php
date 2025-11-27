<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RoadmapNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'topic_id',
        'content'
    ];

    public function topic()
    {
        return $this->belongsTo(RoadmapTopic::class, 'topic_id');
    }
}
