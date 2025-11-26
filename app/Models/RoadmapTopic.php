<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RoadmapTopic extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status'
    ];

    public function steps()
    {
        return $this->hasMany(RoadmapStep::class, 'topic_id')->orderBy('order');
    }

    public function notes()
    {
        return $this->hasMany(RoadmapNote::class, 'topic_id');
    }
}
