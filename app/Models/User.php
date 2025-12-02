<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
class User extends Authenticatable
{
use HasApiTokens, HasFactory, Notifiable;
    protected $fillable = [
        'name',
        'email',
        'password',
        'plain_password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relationships
    public function dailyTasks()
    {
        return $this->hasMany(DailyTask::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function plans()
    {
        return $this->hasMany(Plan::class);
    }

    public function interviewQuestions()
    {
        return $this->hasMany(InterviewQuestion::class);
    }

    public function tools()
    {
        return $this->hasMany(Tool::class);
    }

    public function roadmapTopics()
    {
        return $this->hasMany(RoadmapTopic::class);
    }

    public function programmings()
    {
        return $this->hasMany(Programmings::class);
    }
}
