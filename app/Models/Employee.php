<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Testing\Fluent\Concerns\Has;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'activity_schedule_id',
        'user_id',
        'crew',
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function activitySchedules()
    {
        return $this->hasMany(ActivitySchedule::class);
    }
}
