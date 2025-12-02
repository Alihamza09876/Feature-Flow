<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InterviewQuestion extends Model
{
    protected $fillable = ['user_id', 'language', 'question', 'answer'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
