<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'type',
        'planned_date',
        'is_completed',
    ];

    protected $casts = [
        'planned_date' => 'date',
        'is_completed' => 'boolean',
    ];
}
