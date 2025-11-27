<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyTask extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'task_date',
        'start_time',
        'end_time',
        'status'
    ];
}
